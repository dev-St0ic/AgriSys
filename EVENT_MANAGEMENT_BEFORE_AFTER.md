# Event Management - Before & After Code Comparison

## 1. Store Method - Default Status Change

### BEFORE (Old Logic)
```php
// Lines 167 in EventController.php (OLD)
$isActiveRequest = $request->category === 'announcement' ? true : $request->boolean('is_active', true);
$wasDeactivated = false;
$deactivatedCount = 0;

// Check if creating an active event in this category
if ($isActiveRequest) {
    $activeCountInCategory = Event::where('category', $request->category)
        ->active()
        ->notArchived()
        ->count();

    // If we already have 3 active events in this category, deactivate the oldest one
    if ($activeCountInCategory >= 3) {
        $oldestEvent = Event::where('category', $request->category)
            ->active()
            ->notArchived()
            ->orderBy('created_at', 'asc')
            ->first();

        if ($oldestEvent) {
            $oldestEvent->update([
                'is_active' => false,
                'updated_by' => auth()->id(),
                'updated_at' => now()
            ]);
            $deactivatedCount = 1;
            $wasDeactivated = true;
        }
    }
}
```

### AFTER (New Logic)
```php
// Lines 167 in EventController.php (NEW)
// NEW LOGIC: All new events default to INACTIVE
// Only if explicitly requested and not at category limit, can be active
$isActiveRequest = false;

// Check if user explicitly wants this event active
if ($request->boolean('is_active', false)) {
    // Check if this category already has 1 active event
    $activeCountInCategory = Event::where('category', $request->category)
        ->active()
        ->notArchived()
        ->count();

    // Only allow activation if no active event exists in this category
    if ($activeCountInCategory < 1) {
        $isActiveRequest = true;
    } else {
        // Cannot create as active - category already has an active event
        return response()->json([
            'success' => false,
            'message' => 'The ' . ucfirst($request->category) . ' category already has 1 active event. Please deactivate it first if you want to activate this new event.',
            'warning_type' => 'category_active_event_exists'
        ], 422);
    }
}
```

**Key Differences**:
- ✅ Default changed from `true` to `false`
- ✅ Announcement special case removed
- ✅ Auto-deactivation removed
- ✅ New validation for 1 per category limit
- ✅ Clear error message with warning type

---

## 2. Store Method - Success Response

### BEFORE (Old Logic)
```php
// Lines 245-264 in EventController.php (OLD)
// Build success message
$message = 'Event "' . $event->title . '" created successfully';
if ($request->category === 'announcement') {
    $message .= ' (Announcements are always active)';
}
if ($wasDeactivated) {
    $message .= '. An older event in the ' . ucfirst($request->category) . ' category was auto-deactivated (max 3 active per category)';
}

return response()->json([
    'success' => true,
    'message' => $message,
    'event' => $event->load('creator'),
    'auto_deactivated' => $wasDeactivated,
    'deactivated_count' => $deactivatedCount,
    'category' => $event->category
], 201);
```

### AFTER (New Logic)
```php
// Lines 245-264 in EventController.php (NEW)
// Build success message
$message = 'Event "' . $event->title . '" created successfully';
if (!$event->is_active) {
    $message .= ' (set to inactive - you can activate it later)';
}

return response()->json([
    'success' => true,
    'message' => $message,
    'event' => $event->load('creator'),
    'category' => $event->category
], 201);
```

**Key Differences**:
- ✅ Removed announcement message
- ✅ Added inactive status clarification
- ✅ Removed auto-deactivation response fields
- ✅ Simpler, clearer message

---

## 3. Toggle Status Method - Complete Rewrite

### BEFORE (Old Logic - 100+ lines)
```php
/**
 * Toggle event active status
 * SAFETY RULES:
 * - Announcements can NEVER be deactivated
 * - Cannot deactivate if it's the only active event overall
 * - Cannot deactivate if it's the only active event in its category
 * - Cannot activate if category already has 3 active events
 */
public function toggleStatus(Event $event)
{
    try {
        $newStatus = !$event->is_active;

        // RULE 1: Announcements must always be active - CANNOT toggle
        if ($event->category === 'announcement') {
            return response()->json([
                'success' => false,
                'message' => '🚫 Announcements must always be active...',
                'warning_type' => 'announcement_always_active'
            ], 422);
        }

        // RULE 2: If trying to DEACTIVATE, check if it's the only active announcement
        if ($event->is_active && !$newStatus && $event->category === 'announcement') {
            $activeAnnouncementCount = Event::where('category', 'announcement')
                ->active()
                ->notArchived()
                ->count();

            if ($activeAnnouncementCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cannot deactivate the only active announcement...',
                    'warning_type' => 'last_active_event'
                ], 422);
            }
        }

        // RULE 2B: If trying to DEACTIVATE, check if it's the only active event overall
        if ($event->is_active && !$newStatus) {
            $activeCount = Event::active()->count();

            if ($activeCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cannot deactivate the last active event...',
                    'warning_type' => 'last_active_event'
                ], 422);
            }
        }

        // RULE 3: If trying to DEACTIVATE, check if it's the only active event in category
        if ($event->is_active && !$newStatus) {
            $activeCountInCategory = Event::where('category', $event->category)
                ->active()
                ->notArchived()
                ->count();

            if ($activeCountInCategory <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ This is the only active event in the ' . ucfirst($event->category) . ' category...',
                    'warning_type' => 'last_active_in_category'
                ], 422);
            }
        }

        // RULE 4: If trying to ACTIVATE, check if category already has 3 active
        if (!$event->is_active && $newStatus) {
            $activeCountInCategory = Event::where('category', $event->category)
                ->active()
                ->notArchived()
                ->count();

            if ($activeCountInCategory >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ The ' . ucfirst($event->category) . ' category already has 3 active events...',
                    'warning_type' => 'category_limit_reached'
                ], 422);
            }
        }

        // All checks passed - update status
        $event->update([
            'is_active' => $newStatus,
            'updated_by' => auth()->id()
        ]);

        // ... rest of code
    }
}
```

### AFTER (New Logic - Simplified)
```php
/**
 * Toggle event active status
 * NEW LOGIC:
 * - Only 1 ACTIVE event per category (maximum)
 * - Cannot activate if category already has 1 active (must deactivate that one first)
 * - Can deactivate any event (even if it's the only active in category)
 * - Provides clear notification when trying to activate
 */
public function toggleStatus(Event $event)
{
    try {
        $newStatus = !$event->is_active;

        // If trying to ACTIVATE
        if (!$event->is_active && $newStatus) {
            // Check if this category already has an active event
            $activeCountInCategory = Event::where('category', $event->category)
                ->active()
                ->notArchived()
                ->count();

            if ($activeCountInCategory >= 1) {
                // Get the active event details for the notification
                $activeEvent = Event::where('category', $event->category)
                    ->active()
                    ->notArchived()
                    ->first();

                return response()->json([
                    'success' => false,
                    'message' => 'The ' . ucfirst($event->category) . ' category already has 1 active event: "' . $activeEvent->title . '". Please deactivate it first if you want to activate this event.',
                    'warning_type' => 'only_one_active_allowed',
                    'active_event' => [
                        'id' => $activeEvent->id,
                        'title' => $activeEvent->title
                    ]
                ], 422);
            }
        }

        // All checks passed - update status
        $event->update([
            'is_active' => $newStatus,
            'updated_by' => auth()->id()
        ]);

        \Log::info('📅 [Events] Event status toggled', [
            'id' => $event->id,
            'category' => $event->category,
            'new_status' => $newStatus ? 'active' : 'inactive'
        ]);

        return response()->json([
            'success' => true,
            'message' => $newStatus
                ? '✅ Event "' . $event->title . '" is now active'
                : '✅ Event "' . $event->title . '" is now inactive',
            'is_active' => $newStatus,
            'category' => $event->category
        ]);

    } catch (\Exception $e) {
        \Log::error('📅 [Events] Failed to toggle event status', [
            'id' => $event->id,
            'message' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to update status'
        ], 500);
    }
}
```

**Key Differences**:
- ✅ Removed 4 complex deactivation rules
- ✅ Removed announcement special case
- ✅ Removed overall active event check
- ✅ Changed activation limit from 3 to 1
- ✅ Added active event details to error response
- ✅ Can always deactivate (no restrictions)
- ✅ 60% less code, much clearer logic
- ✅ New warning type: `only_one_active_allowed`

---

## 4. Blade Form - Status Field

### BEFORE (Old HTML)
```html
<div class="col-md-6 mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-select">
        <option value="1" selected>Active</option>
        <option value="0">Inactive</option>
    </select>
</div>
```

### AFTER (New HTML)
```html
<div class="col-md-6 mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-select">
        <option value="0" selected>Inactive (Default)</option>
        <option value="1">Active</option>
    </select>
    <small class="text-muted d-block mt-1">
        <i class="fas fa-info-circle me-1"></i>
        Only 1 active event per category. New events default to inactive.
    </small>
</div>
```

**Key Differences**:
- ✅ Default changed to "Inactive (Default)"
- ✅ Added helpful helper text
- ✅ Clearer UI for users

---

## 5. JavaScript - Create Event Handler

### BEFORE (Old Logic)
```javascript
document.getElementById('createEventForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const category = document.querySelector('select[name="category"]').value;
    const isActive = document.querySelector('select[name="is_active"]').value;

    // FRONTEND VALIDATION: Announcements are always active
    if (category === 'announcement' && isActive === '0') {
        showToast('warning',
            'Announcements must always be active. Status has been automatically set to Active.');
        document.querySelector('select[name="is_active"]').value = '1';
        return;
    }

    // ... form submission code

    if (!response.ok) {
        if (data.warning_type === 'category_limit_reached') {
            throw {
                message: data.message +
                    ' You can create it as inactive or deactivate an existing event first.',
                warningType: data.warning_type
            };
        }
        throw new Error(data.message || 'Failed to create event');
    }

    // ... success handling
});
```

### AFTER (New Logic)
```javascript
document.getElementById('createEventForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const category = document.querySelector('select[name="category"]').value;
    const isActive = document.querySelector('select[name="is_active"]').value;

    // NEW LOGIC: Only 1 active event per category
    // If user wants to create as active, backend will validate
    if (isActive === '1') {
        // Just show a note that frontend will check on response
        // Backend will validate if category already has active event
    }

    // ... form submission code

    if (!response.ok) {
        if (data.warning_type === 'category_active_event_exists') {
            // New logic: Cannot create as active if category already has active event
            throw {
                message: data.message,
                warningType: data.warning_type
            };
        }
        throw new Error(data.message || 'Failed to create event');
    }

    // ... success handling
});
```

**Key Differences**:
- ✅ Removed announcement forced-active logic
- ✅ Updated error handling for new warning type
- ✅ Simplified, cleaner code

---

## 6. JavaScript - Toggle Event Handler

### BEFORE (Old Logic)
```javascript
async function toggleEvent(eventId) {
    try {
        const response = await fetch(`/admin/events/${eventId}/toggle-status`, {
            method: 'PATCH',
            headers: { /* ... */ }
        });
        const data = await response.json();

        if (!response.ok) {
            if (data.warning_type === 'announcement_always_active') {
                showToast('warning', data.message);
            } else if (data.warning_type === 'last_active_in_category') {
                showToast('info', data.message);
            } else if (data.warning_type === 'category_limit_reached') {
                showToast('info', data.message);
            } else if (data.warning_type === 'last_active_event') {
                showToast('info', data.message);
            } else {
                throw new Error(data.message || 'Failed to update status');
            }
            return;
        }

        showSuccess(data.message);
        setTimeout(() => location.reload(), 800);
    } catch (error) {
        showError(error.message);
    }
}
```

### AFTER (New Logic)
```javascript
async function toggleEvent(eventId) {
    try {
        const response = await fetch(`/admin/events/${eventId}/toggle-status`, {
            method: 'PATCH',
            headers: { /* ... */ }
        });
        const data = await response.json();

        if (!response.ok) {
            if (data.warning_type === 'only_one_active_allowed') {
                // New logic: Only 1 active event per category
                const activeEvent = data.active_event ? data.active_event.title : 'another event';
                showWarning(data.message);
            } else if (data.warning_type === 'announcement_always_active') {
                showToast('warning', data.message);
            } else if (data.warning_type === 'last_active_in_category') {
                showToast('info', data.message);
            } else if (data.warning_type === 'category_limit_reached') {
                showToast('info', data.message);
            } else if (data.warning_type === 'last_active_event') {
                showToast('info', data.message);
            } else {
                throw new Error(data.message || 'Failed to update status');
            }
            return;
        }

        showSuccess(data.message);
        setTimeout(() => location.reload(), 800);
    } catch (error) {
        showError(error.message);
    }
}
```

**Key Differences**:
- ✅ Added handling for new warning type: `only_one_active_allowed`
- ✅ Kept old warning types for backward compatibility
- ✅ Better error messaging with active event info

---

## Summary Table

| Aspect | Before | After |
|--------|--------|-------|
| **Active events per category** | Max 3 | Max 1 |
| **New event default** | Active | Inactive |
| **Announcement special rule** | Always active | Same as others |
| **Can deactivate only active** | ❌ Blocked | ✅ Allowed |
| **Auto-deactivation** | Yes | No |
| **Activation validation** | 3-event limit | 1-event limit |
| **Deactivation validation** | 4 rules | None |
| **Error messages** | Generic | Specific (includes active event name) |
| **Code complexity** | High | Low |
| **User control** | Limited | Full |
