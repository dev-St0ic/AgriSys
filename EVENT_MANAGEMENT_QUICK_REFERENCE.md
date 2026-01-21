# Event Management New Logic - Quick Reference

## 🎯 Core Rule
**Only 1 ACTIVE event per category at any time**

## 📊 Categories
- Announcement
- Ongoing  
- Upcoming
- Past

## ✅ What Works

### Creating Events
✓ Create as INACTIVE (default) - **Always succeeds**
✓ Create as ACTIVE - Succeeds **only if no active event in category**
✓ Multiple INACTIVE events can exist in same category

### Toggling Status
✓ **Deactivate** any event - Always succeeds (no restrictions)
✓ **Activate** event - Succeeds only if no active event in category
✓ Can have 0 active events in category

## ❌ What Fails

### Creating Events
✗ Create as ACTIVE when category already has 1 active
  → Error: "Category already has 1 active event: [Title]. Please deactivate it first."

### Toggling Status
✗ Activate when category already has 1 active
  → Error: "Category already has 1 active event: [Title]. Please deactivate it first."

## 🔄 Workflow: Switch Active Event

```
Before:  Event A (Active) + Event B (Inactive)
Goal:    Make Event B active instead

Steps:
1. Click toggle on Event A → Deactivate ✓
2. Click toggle on Event B → Activate ✓

After:   Event A (Inactive) + Event B (Active)
```

## 📝 API Warning Types

| Warning Type | Scenario | Action |
|---|---|---|
| `only_one_active_allowed` | Trying to activate when category has 1 active | Deactivate the existing active event first |
| `category_active_event_exists` | Creating as active when category has 1 active | Create as inactive OR deactivate existing first |

## 💡 Key Differences from Old Logic

| Aspect | Old Logic | New Logic |
|---|---|---|
| Active events per category | Max 3 | Max 1 |
| New event default status | Active | **Inactive** |
| Can deactivate only active event | ❌ Blocked | ✅ Allowed |
| Announcements special rule | Always active (mandatory) | Same as others |
| Auto-deactivation | Yes (oldest auto-removed) | No (manual control) |

## 🚀 Common Tasks

### Activate a Different Event in Same Category
1. Deactivate current active event
2. Activate desired event

### Prepare Multiple Events for Later
1. Create all as Inactive
2. Activate one when ready
3. Later switch by: Deactivate old → Activate new

### Create New Event Without Disrupting Current
1. Create as Inactive (automatic default)
2. Activate only when ready to go live

## 📱 User Interface Notes

- **Create Event Modal**
  - Status field defaults to "Inactive"
  - Helper text: "Only 1 active event per category"

- **Toggle Button**
  - Available in More Actions dropdown (⋮)
  - Shows "Activate" or "Deactivate" based on current status
  - Clear error message if activation blocked

- **Toast Notifications**
  - Success: "Event is now active/inactive"
  - Error: Full message explaining why and what's blocking
