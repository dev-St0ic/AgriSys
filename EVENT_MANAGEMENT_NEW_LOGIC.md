# Event Management - New Logic Implementation

## Overview
The event management system has been updated with a new, simplified logic focused on maintaining **only 1 active event per category** at a time.

## Key Changes

### 1. **Default Status for New Events**
- **Previous Logic**: New events defaulted to ACTIVE status
- **New Logic**: New events default to **INACTIVE** status
- **Benefit**: Admins have full control before activating events

### 2. **One Active Event Per Category**
- **Limit**: Only **1 active event per category** is allowed at any time
- **Categories**: Announcement, Ongoing, Upcoming, Past
- **Enforcement**: 
  - Cannot create a new event as Active if the category already has an active event
  - Cannot toggle/activate an event if the category already has 1 active event
  - Can deactivate any event, even if it's the only active one

### 3. **Create Event Behavior**

#### Creating an Event as INACTIVE (Default)
```
✓ Always succeeds
✓ Event added to database in INACTIVE state
✓ User can activate later manually
```

#### Creating an Event as ACTIVE
```
✓ Succeeds ONLY if no other active event exists in that category
✗ Fails if category already has 1 active event
  Message: "The [Category] category already has 1 active event: '[Event Title]'. 
           Please deactivate it first if you want to activate this event."
```

### 4. **Toggle Status Behavior**

#### Activating an Event (Inactive → Active)
```
✓ Succeeds ONLY if no active event exists in that category
✗ Fails if 1 active event already exists in category
  Message: "The [Category] category already has 1 active event: '[Event Title]'. 
           Please deactivate it first if you want to activate this event."
```

#### Deactivating an Event (Active → Inactive)
```
✓ Always succeeds
✓ No restrictions or validations
✓ Can have 0 active events in a category
```

### 5. **Notification System**

#### Warning Type: `only_one_active_allowed`
Triggered when:
- User tries to create an event as Active but category has 1 active event
- User tries to activate an event but category has 1 active event

Response includes:
```json
{
  "success": false,
  "message": "The [Category] category already has 1 active event: '[Title]'. Please deactivate it first...",
  "warning_type": "only_one_active_allowed",
  "active_event": {
    "id": 123,
    "title": "Event Title"
  }
}
```

#### Warning Type: `category_active_event_exists`
Triggered when:
- Creating an event with `is_active=1` but category already has an active event

## User Interface Updates

### Create Event Modal
- **Status field now shows**:
  - Default: "Inactive (Default)" ✓
  - Option: "Active"
- **Helper text added**:
  > "Only 1 active event per category. New events default to inactive."

### Toggle Button
- **Deactivate action**: Always available, no restrictions
- **Activate action**: 
  - ✓ Available if no active event in category
  - ✗ Blocked if 1 active event already exists
  - Shows clear error message with active event details

## Workflow Example

### Scenario 1: Switch Active Event in a Category
```
1. Category "Upcoming" has Event A (Active) and Event B (Inactive)
2. Admin wants to activate Event B instead
3. Actions needed:
   - Click toggle on Event A → Deactivate (succeeds)
   - Click toggle on Event B → Activate (succeeds)
4. Result: Event A now Inactive, Event B now Active
```

### Scenario 2: Create Multiple Events, Activate One
```
1. Admin creates Event X (defaults to Inactive)
2. Admin creates Event Y (defaults to Inactive)
3. Admin creates Event Z (defaults to Inactive)
4. Admin clicks activate on Event X (succeeds - first active in category)
5. Admin clicks activate on Event Y (fails - category already has active)
   Message: "The category already has 1 active event: Event X..."
6. To activate Event Y:
   - First deactivate Event X
   - Then activate Event Y
```

## Backend Changes

### EventController::store()
- Changed validation: No longer enforces 3 active events per category
- New default: `is_active = false` for all new events (except removed logic)
- New validation: If `is_active=true` requested, checks if category already has active event
- Returns error if trying to create Active when category limit reached

### EventController::toggleStatus()
- **Removed logic**:
  - Announcements must always be active
  - Cannot deactivate if only active event overall
  - Cannot deactivate if only active event in category
  - Cannot activate if category has 3 active events
- **New logic**:
  - If trying to ACTIVATE: Check if category has 1 active event
    - Succeeds only if count < 1
    - Fails with `only_one_active_allowed` warning if count >= 1
  - If trying to DEACTIVATE: Always succeeds
  - Include active event details in error response

## Frontend Changes

### JavaScript - toggleEvent()
- Added handling for new warning type: `only_one_active_allowed`
- Shows error message with active event title
- Triggers page reload on success

### JavaScript - createEventForm
- Removed announcement validation (auto-active requirement)
- Added handling for `category_active_event_exists` warning type
- Default status selector value changed to "0" (Inactive)

## Migration Notes

⚠️ **No database migrations required**
- Uses existing `is_active` column
- No schema changes needed

## Testing Checklist

- [ ] Create event as Inactive (default) - should succeed
- [ ] Create event as Active with no active event in category - should succeed
- [ ] Create event as Active with 1 active event in category - should fail
- [ ] Toggle Active event to Inactive - should succeed
- [ ] Toggle Inactive event to Active with no active in category - should succeed
- [ ] Toggle Inactive event to Active with 1 active in category - should fail
- [ ] Multiple events can exist Inactive in one category
- [ ] Only 1 event can be Active in one category
- [ ] Different categories can each have their own active event
- [ ] Error messages display correctly with active event details

## API Response Examples

### Success: Create Event as Inactive
```json
{
  "success": true,
  "message": "Event \"Sample\" created successfully (set to inactive - you can activate it later)",
  "event": { ... },
  "category": "announcement"
}
```

### Success: Toggle to Active
```json
{
  "success": true,
  "message": "Event \"Sample\" is now active",
  "is_active": true,
  "category": "upcoming"
}
```

### Error: Category Limit on Create
```json
{
  "success": false,
  "message": "The Announcement category already has 1 active event. Please deactivate it first if you want to activate this new event.",
  "warning_type": "category_active_event_exists"
}
```

### Error: Category Limit on Toggle
```json
{
  "success": false,
  "message": "The Upcoming category already has 1 active event: \"Wedding Expo 2025\". Please deactivate it first if you want to activate this event.",
  "warning_type": "only_one_active_allowed",
  "active_event": {
    "id": 5,
    "title": "Wedding Expo 2025"
  }
}
```

## Summary

✅ **Simpler Logic**: 1 active event per category (down from 3)
✅ **Better Control**: New events default to inactive
✅ **Clear Feedback**: Error messages show which event is blocking activation
✅ **Flexible**: Can have 0 active events in a category
✅ **Intuitive**: No automatic deactivation, user controls everything
