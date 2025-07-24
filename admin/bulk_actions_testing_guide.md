# Bulk Actions Testing Guide

## Setup Required

### 1. Create the Admin Actions Log Table (Optional)
```sql
CREATE TABLE `admin_actions_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` varchar(100) NOT NULL,
  `action_type` enum('bulk_approve','bulk_reject','bulk_delete','bulk_visibility') NOT NULL,
  `client_ids` text NOT NULL COMMENT 'JSON array of affected client IDs',
  `action_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `additional_data` text DEFAULT NULL COMMENT 'Additional action details in JSON format',
  PRIMARY KEY (`id`),
  KEY `idx_admin_action` (`admin_id`, `action_timestamp`),
  KEY `idx_action_type` (`action_type`, `action_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 2. Ensure You Have Pending Registrations
```sql
-- Check pending registrations
SELECT id, name, approval_status FROM clients WHERE approval_status = 'pending' LIMIT 10;

-- If no pending registrations, create some test data or change existing ones to pending
UPDATE clients SET approval_status = 'pending' WHERE id IN (1, 2, 3, 4, 5);
```

## Testing Steps

### Test 1: Basic Bulk Selection

1. **Go to Registrations Dashboard**
   - Navigate to `admin/registrations_dashboard.php`
   - Login as admin if not already logged in

2. **Check for Pending Registrations**
   - Filter by Status: "Pending" to see only pending registrations
   - You should see checkboxes only for pending registrations

3. **Test Individual Selection**
   - ✅ Click on individual checkboxes
   - ✅ Row should highlight with blue background and left border
   - ✅ Bulk actions bar should appear showing "X selected"
   - ✅ Approve and Reject buttons should be enabled

4. **Test Select All**
   - ✅ Click the header checkbox (Select All)
   - ✅ All visible pending registrations should be selected
   - ✅ Bulk actions bar should show total count

### Test 2: Bulk Approve

1. **Select Multiple Pending Registrations**
   - Select 3-5 pending registrations using checkboxes

2. **Click "Approve Selected"**
   - ✅ Confirmation dialog should appear
   - ✅ Click "OK" to proceed

3. **Verify Results**
   - ✅ Success message should appear
   - ✅ Selected registrations should disappear from pending filter
   - ✅ Statistics cards should update
   - ✅ Selection should be cleared

4. **Check Database**
   ```sql
   -- Verify approvals
   SELECT id, name, approval_status, approved_by FROM clients 
   WHERE approval_status = 'approved' 
   ORDER BY id DESC LIMIT 10;
   
   -- Check audit log (if table exists)
   SELECT * FROM admin_actions_log 
   WHERE action_type = 'bulk_approve' 
   ORDER BY action_timestamp DESC LIMIT 5;
   ```

### Test 3: Bulk Reject

1. **Select Different Pending Registrations**
   - Select 2-4 different pending registrations

2. **Click "Reject Selected"**
   - ✅ Confirmation dialog should appear
   - ✅ Click "OK" to proceed

3. **Verify Results**
   - ✅ Success message should appear
   - ✅ Selected registrations should disappear from pending filter
   - ✅ Statistics should update
   - ✅ Selection should be cleared

4. **Check Database**
   ```sql
   -- Verify rejections
   SELECT id, name, approval_status, rejected_by, rejected_at FROM clients 
   WHERE approval_status = 'rejected' 
   ORDER BY rejected_at DESC LIMIT 10;
   
   -- Check audit log (if table exists)
   SELECT * FROM admin_actions_log 
   WHERE action_type = 'bulk_reject' 
   ORDER BY action_timestamp DESC LIMIT 5;
   ```

### Test 4: Mixed Status Handling

1. **Filter to Show All Statuses**
   - Clear status filter to show all registrations

2. **Try to Select Non-Pending Registrations**
   - ✅ Approved/Rejected registrations should NOT have checkboxes
   - ✅ Only pending registrations should be selectable

3. **Select Mix of Pending Only**
   - ✅ Should only be able to select pending registrations
   - ✅ Bulk actions should work normally

### Test 5: Error Handling

1. **Test with No Selection**
   - Don't select any registrations
   - Click "Approve Selected" or "Reject Selected"
   - ✅ Should show warning: "No registrations selected"

2. **Test Cancel Action**
   - Select some registrations
   - Click bulk action button
   - Click "Cancel" in confirmation dialog
   - ✅ No action should be performed
   - ✅ Selection should remain

3. **Test Network Error Simulation**
   - Open browser developer tools
   - Go to Network tab and set to "Offline"
   - Try bulk action
   - ✅ Should show error message
   - ✅ Button should restore to normal state

### Test 6: UI/UX Features

1. **Test Loading States**
   - Select registrations and perform bulk action
   - ✅ Button should show spinner and "Processing..." text
   - ✅ Button should be disabled during processing

2. **Test Selection Clearing**
   - Select some registrations
   - Use filters or pagination
   - ✅ Selection should be cleared automatically

3. **Test Responsive Design**
   - Test on mobile/tablet view
   - ✅ Bulk actions should be responsive
   - ✅ Checkboxes should be easily clickable

### Test 7: Performance Testing

1. **Test with Many Selections**
   - If you have 20+ pending registrations, select all
   - Perform bulk action
   - ✅ Should handle large selections efficiently

2. **Test Pagination with Selection**
   - Select items on page 1
   - Go to page 2
   - ✅ Selection should be cleared
   - ✅ No conflicts between pages

## Expected Results Summary

### ✅ What Should Work:
- Only pending registrations show checkboxes
- Individual and "Select All" functionality
- Visual feedback (row highlighting, selection count)
- Bulk approve/reject with confirmation
- Success/error messages
- Statistics updates after bulk actions
- Selection clearing after actions
- Loading states and button disabling
- Audit logging (if table created)

### ❌ What Should NOT Happen:
- Selecting already approved/rejected registrations
- Bulk actions without confirmation
- UI freezing during operations
- Data inconsistency after bulk operations
- Selection persisting across page changes

## Troubleshooting

### Issue: No checkboxes visible
**Solution**: Ensure you have pending registrations
```sql
UPDATE clients SET approval_status = 'pending' WHERE id IN (1,2,3,4,5);
```

### Issue: Bulk actions not working
**Check**: 
1. Browser console for JavaScript errors
2. Network tab for failed requests
3. PHP error logs for server-side issues

### Issue: Statistics not updating
**Solution**: The page reloads after 2 seconds to update statistics

### Issue: Audit log errors
**Solution**: Either create the `admin_actions_log` table or remove the logging code from `bulk_actions.php`

## Database Queries for Verification

```sql
-- Check current status distribution
SELECT approval_status, COUNT(*) as count 
FROM clients 
GROUP BY approval_status;

-- Check recent bulk actions (if audit table exists)
SELECT 
    admin_id,
    action_type,
    JSON_LENGTH(client_ids) as affected_count,
    action_timestamp
FROM admin_actions_log 
ORDER BY action_timestamp DESC 
LIMIT 10;

-- Reset test data (if needed)
UPDATE clients SET approval_status = 'pending' 
WHERE id IN (1,2,3,4,5,6,7,8,9,10);
``` 