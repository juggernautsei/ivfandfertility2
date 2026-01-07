# Fee Sheet Price Population Fix - Testing Instructions

## Issue Description
CPT4 codes added via the search dropdown sometimes fail to populate with prices, appearing as $0 instead of the correct price from the prices table.

## Root Cause
The price lookup in `FeeSheet.class.php` (`addServiceLineItem` method, lines 490-532) was failing when:
1. The specific price level didn't have a price record
2. The query returned NULL for `pr_price`
3. No fallback to default price level

## Changes Made

### 1. library/FeeSheet.class.php
**Lines 525-531**: Added fallback logic and error logging
- Now falls back to the default price level if the patient's specific price level has no price
- Logs when prices cannot be found to help diagnose missing price records
- Prevents codes from being added with $0 price when prices exist in other levels

### 2. interface/forms/fee_sheet/review/js/fee_sheet_core.js  
**Lines 76, 83, 92, 96-106**: Added console logging
- Tracks the code addition workflow
- Logs when codes are added, saved, and displayed
- Warns in console when price fields are zero or empty after adding codes

## Testing Steps

### Prerequisites
1. Ensure you have a test patient encounter open
2. Have several CPT4 codes with prices in the `prices` table
3. Open browser developer tools (F12) and go to Console tab

### Test Case 1: Normal Code Addition (Happy Path)
1. Navigate to Fee Sheet for a patient encounter
2. Open Console in browser dev tools
3. Use the search bar to find a CPT4 code (e.g., "Ferritin" for code 82728)
4. Select the code from the dropdown
5. **Expected Results:**
   - Console shows: "FeeSheet: Adding code - CPT4|82728|"
   - Console shows: "FeeSheet: Code added, refreshing display"
   - Console shows: "FeeSheet: Saving code"
   - Console shows: "FeeSheet: Code saved, final update"
   - The code appears in the table WITH a price populated
   - NO console warnings about "Price is zero or empty"

### Test Case 2: Multiple Code Additions
1. Add 5-10 different codes in quick succession via the search dropdown
2. **Expected Results:**
   - All codes should have prices populated
   - Console should show the workflow for each code
   - No warnings about empty prices

### Test Case 3: Price Level Mismatch
1. Check the patient's price level (should be displayed at top of fee sheet)
2. Find a code that doesn't have a price for that specific level but has a default price
3. Add the code
4. **Expected Results:**
   - Code should populate with the default price level price
   - Check Apache error log for: "FeeSheet: Price level '...' not found for code_id=..., using default price: ..."
   - The code should NOT appear with $0

### Test Case 4: Missing Price Records
1. Identify or create a code with NO price records in the prices table
2. Add this code via search
3. **Expected Results:**
   - Code appears with $0 price (expected behavior - no price exists)
   - Check Apache error log for: "FeeSheet: No price found for code_id=..., code=..., codetype=..., pricelevel=..."
   - Console shows warning: "FeeSheet: Price is zero or empty for: [code description]"

### Test Case 5: Rapid Code Selection
1. Quickly select multiple codes one after another (stress test)
2. **Expected Results:**
   - All AJAX calls should complete
   - All codes should have prices if prices exist in database
   - No race conditions or missing prices

## Monitoring Tools

### Browser Console
- Open: F12 → Console tab
- Look for: "FeeSheet:" prefixed messages
- Warnings will appear if prices are zero/empty

### Apache Error Log
```bash
# For the openemr703 installation
tail -f /var/log/apache2/error.log | grep "FeeSheet:"
```

Look for:
- "Price level '...' not found" - indicates fallback to default price
- "No price found" - indicates missing price records in database

### Database Verification
```sql
-- Check if prices exist for a specific code
SELECT c.id, c.code, c.code_text, p.pr_level, p.pr_price 
FROM codes c 
LEFT JOIN prices p ON c.id = p.pr_id 
WHERE c.code = '82728' AND c.code_type = 4;

-- Check available price levels
SELECT * FROM list_options WHERE list_id = 'pricelevel' AND activity = 1;
```

## Success Criteria
✅ Prices populate consistently (95%+ success rate)
✅ Console logging provides clear visibility into workflow
✅ Fallback to default price level works when specific level unavailable
✅ Error logging helps identify missing price records
✅ No JavaScript errors in console
✅ No PHP errors in Apache error log

## Rollback Plan
If issues occur:
1. Revert changes to `library/FeeSheet.class.php` (lines 525-531)
2. Revert changes to `interface/forms/fee_sheet/review/js/fee_sheet_core.js` (console.log additions)
3. Restart Apache/PHP-FPM if necessary

## Notes
- The fix does not create missing price records - it only provides better fallback behavior
- Price records still need to be created in the `prices` table via the fee sheet configuration
- The logging is intentionally verbose during testing and can be reduced after verification
