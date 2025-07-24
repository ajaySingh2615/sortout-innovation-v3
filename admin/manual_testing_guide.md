# Manual Testing Guide for Alert Tracking System

## Step 1: Setup the Database Table

First, run this SQL in your database:

```sql
-- Create the alert tracking table
CREATE TABLE `admin_alert_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` varchar(100) NOT NULL,
  `last_seen_timestamp` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_admin` (`admin_id`),
  KEY `idx_admin_timestamp` (`admin_id`,`last_seen_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert initial tracking for existing admins
INSERT IGNORE INTO `admin_alert_tracking` (`admin_id`, `last_seen_timestamp`)
SELECT DISTINCT `username`, NOW()
FROM `users` 
WHERE `role` IN ('admin', 'super_admin');
```

## Step 2: Verify Table Creation

Check if the table was created successfully:

```sql
-- Check if table exists
SHOW TABLES LIKE 'admin_alert_tracking';

-- Check table structure
DESCRIBE admin_alert_tracking;

-- Check initial data
SELECT * FROM admin_alert_tracking;
```

## Step 3: Test Scenario 1 - New Admin Login (No Alert)

1. **Login as admin** to the registrations dashboard
2. **Expected Result**: No alert should show (since all existing registrations are marked as "seen")
3. **Verify in database**:
   ```sql
   -- Check your admin's tracking record
   SELECT * FROM admin_alert_tracking WHERE admin_id = 'your_admin_username';
   ```

## Step 4: Test Scenario 2 - Create New Registration

1. **Add a new client registration** (through your registration form)
2. **Don't refresh the dashboard yet**
3. **Verify in database**:
   ```sql
   -- Check the new registration
   SELECT id, name, created_at FROM clients ORDER BY created_at DESC LIMIT 1;
   
   -- Check if it's newer than admin's last seen time
   SELECT 
       c.id, 
       c.name, 
       c.created_at as registration_time,
       a.last_seen_timestamp as admin_last_seen,
       CASE 
           WHEN c.created_at > a.last_seen_timestamp THEN 'NEW' 
           ELSE 'SEEN' 
       END as status
   FROM clients c
   CROSS JOIN admin_alert_tracking a 
   WHERE a.admin_id = 'your_admin_username'
   ORDER BY c.created_at DESC 
   LIMIT 5;
   ```

## Step 5: Test Scenario 3 - Dashboard Alert Appears

1. **Refresh the registrations dashboard**
2. **Expected Results**:
   - ✅ Alert banner should appear: "New Registrations Alert! 1 new registration(s) since your last visit"
   - ✅ "View New Registrations" button should be visible
   - ✅ "Mark as Seen" button should be visible

## Step 6: Test Scenario 4 - View New Registrations Filter

1. **Click "View New Registrations" button**
2. **Expected Results**:
   - ✅ Date filter should change to "Since Last Visit"
   - ✅ Only new registrations should be displayed
   - ✅ New registrations should have "NEW" badge and golden background
   - ✅ Alert should disappear after viewing

## Step 7: Test Scenario 5 - Mark as Seen

1. **Add another new registration**
2. **Refresh dashboard** (alert should appear again)
3. **Click "Mark as Seen" button**
4. **Expected Results**:
   - ✅ Alert should disappear immediately
   - ✅ Database should update the timestamp
   
   **Verify in database**:
   ```sql
   -- Check updated timestamp
   SELECT admin_id, last_seen_timestamp, updated_at 
   FROM admin_alert_tracking 
   WHERE admin_id = 'your_admin_username';
   ```

## Step 8: Test Scenario 6 - Multiple Admins

1. **Login as different admin** (if you have multiple admin accounts)
2. **Expected Results**:
   - ✅ Each admin should have separate alert tracking
   - ✅ One admin marking as seen shouldn't affect other admins
   
   **Verify in database**:
   ```sql
   -- Check all admin tracking records
   SELECT * FROM admin_alert_tracking ORDER BY admin_id;
   ```

## Step 9: Test Scenario 7 - Time-based Indicators

1. **Add a new registration**
2. **Wait 5 minutes**
3. **Refresh dashboard**
4. **Expected Results**:
   - ✅ Registration should show "5m ago" in time column
   - ✅ Should have "NEW" badge if within 2 hours
   - ✅ Should have golden background animation

## Step 10: Test Database Queries Manually

Run these queries to understand the system:

```sql
-- 1. Find all new registrations for a specific admin
SELECT 
    c.id,
    c.name,
    c.created_at,
    'NEW' as status
FROM clients c
JOIN admin_alert_tracking a ON a.admin_id = 'your_admin_username'
WHERE c.created_at > a.last_seen_timestamp
ORDER BY c.created_at DESC;

-- 2. Count new registrations per admin
SELECT 
    a.admin_id,
    a.last_seen_timestamp,
    COUNT(c.id) as new_registrations_count
FROM admin_alert_tracking a
LEFT JOIN clients c ON c.created_at > a.last_seen_timestamp
GROUP BY a.admin_id, a.last_seen_timestamp;

-- 3. Simulate marking as seen (update timestamp)
UPDATE admin_alert_tracking 
SET last_seen_timestamp = NOW() 
WHERE admin_id = 'your_admin_username';

-- 4. Check registrations from today
SELECT 
    id, 
    name, 
    created_at,
    TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours_ago
FROM clients 
WHERE DATE(created_at) = CURDATE()
ORDER BY created_at DESC;
```

## Step 11: Test Error Scenarios

1. **Test with non-existent admin**:
   ```sql
   -- This should create a new tracking record
   SELECT * FROM admin_alert_tracking WHERE admin_id = 'non_existent_admin';
   ```

2. **Test with deleted client**:
   - Delete a client from database
   - Refresh dashboard
   - Should work without errors

## Step 12: Performance Testing

1. **Add many registrations** (10-20)
2. **Test dashboard loading speed**
3. **Check if pagination works correctly**
4. **Verify filters work with new registrations**

## Troubleshooting Common Issues

### Issue 1: Alert not showing
**Check**:
```sql
-- Verify admin tracking exists
SELECT * FROM admin_alert_tracking WHERE admin_id = 'your_username';

-- Check if there are newer registrations
SELECT COUNT(*) FROM clients c, admin_alert_tracking a 
WHERE a.admin_id = 'your_username' AND c.created_at > a.last_seen_timestamp;
```

### Issue 2: Alert always showing
**Fix**:
```sql
-- Update admin's last seen timestamp
UPDATE admin_alert_tracking 
SET last_seen_timestamp = NOW() 
WHERE admin_id = 'your_username';
```

### Issue 3: Multiple alerts for same admin
**Check**:
```sql
-- Should return only 1 row per admin
SELECT admin_id, COUNT(*) FROM admin_alert_tracking GROUP BY admin_id;
```

## Expected Database State After Testing

After successful testing, your database should have:

```sql
-- Check final state
SELECT 
    a.admin_id,
    a.last_seen_timestamp,
    COUNT(c.id) as total_clients,
    SUM(CASE WHEN c.created_at > a.last_seen_timestamp THEN 1 ELSE 0 END) as unseen_clients
FROM admin_alert_tracking a
LEFT JOIN clients c ON 1=1
GROUP BY a.admin_id, a.last_seen_timestamp;
```

## Success Criteria

✅ **All tests pass if**:
1. Alert appears only when there are new registrations
2. Each admin has independent tracking
3. "Mark as Seen" updates the timestamp correctly
4. Dashboard filters work with alert system
5. No errors in browser console or PHP logs
6. Database queries execute without errors
7. Performance is acceptable with multiple registrations 