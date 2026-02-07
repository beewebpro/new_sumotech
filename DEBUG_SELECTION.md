# Debug: TTS Generation Bug Investigation

## Problem Statement
User selects segment 2 and 3, but:
1. ❌ ALL segments (0-12) are generated
2. ❌ Segment 2 and 3 are ALSO generated (creating 2 files each)

This suggests:
- Either frontend is not sending correct `segment_indices`
- Or backend is processing more segments than received

## What I've Added

### Backend Logging (DubSyncController.php)
- Line 957-966: Enhanced TTS request logging to show exact `segment_indices` received
- Line 1000-1009: Added logging for each segment processing with segment existence check
- Line 1067-1080: Added logging before/after database update showing which segments have audio

### Frontend Logging (edit.blade.php)
- Line 2059-2071: Added detailed console logs showing:
  - Selected segment indices
  - ALL checkbox states (checked/unchecked)
  - Which checkboxes are actually checked

## Testing Instructions

### Step 1: Prepare Browser
1. **Clear Cache**: Ctrl+Shift+Delete → Clear all data
2. **Open DevTools**: Press F12
3. **Go to Console tab**: Click "Console" tab
4. **Go to Network tab**: Keep Network tab open to see API calls

### Step 2: Navigate to Project
1. Go to your project
2. Scroll down to segments section

### Step 3: Select Segments CAREFULLY
1. ✅ Check ONLY segment 2 checkbox
2. ✅ Check ONLY segment 3 checkbox
3. ❌ Do NOT check any other checkboxes
4. Look at console - should show:
   ```
   🎯 Selected segments: 1, 2 (Total: 2)
   📋 ALL CHECKBOX STATES: {0: false, 1: true, 2: true, 3: false, ...}
   ✅ CHECKED CHECKBOXES: [1, 2]
   ```

### Step 4: Click Generate TTS
1. Click "Generate TTS Voice" button
2. Immediately check:
   - **Console tab**: Look for "Bắt đầu tạo TTS cho..." message showing indices
   - **Network tab**: Look for the POST request to `/generate-segment-tts`
     - Click on it
     - Click "Request" tab
     - Look for `segment_indices: [1, 2]`

### Step 5: Wait for Completion
1. Watch progress bar
2. Take screenshot of:
   - Console output
   - Network request payload

### Step 6: Check Results
1. Open File Explorer: `d:\Download\apps\laragon\www\sumotech\public\projects\{projectId}\`
2. Count audio files:
   - Files starting with `s0_` → How many?
   - Files starting with `s1_` → How many?
   - Files starting with `s2_` → How many?
3. Take screenshot

### Step 7: Provide Logs
1. **Console Logs**: Right-click console → Save as HTML → Send to me
2. **Laravel Logs**: Copy last 100 lines from:
   - `storage/logs/laravel.log`

## Expected vs Actual

### Expected (CORRECT BEHAVIOR)
```
🎯 Selected segments: 1, 2 (Total: 2)
✅ CHECKED CHECKBOXES: [1, 2]
Network Request: {"segment_indices":[1,2],...}
Files created: s1_timestamp_provider.wav, s2_timestamp_provider.wav
Laravel Log: segment_indices_requested: [1, 2]
```

### Actual (BUG BEHAVIOR)
```
Files in project directory:
- s0_* (multiple files) ← SHOULDN'T BE HERE
- s1_* (2+ files) ← Only 1 should exist
- s2_* (2+ files) ← Only 1 should exist
- s3_* through s12_* ← All files present! ← SHOULDN'T BE HERE
```

## What to Look For in Logs

### Laravel Log (storage/logs/laravel.log)
Search for these lines:
1. `"TTS Request received"` - See what was sent
2. `"segment_indices_requested":[X,Y]` - See what backend saved
3. `"segments_with_audio":[...]` - See all segments with audio files

Example CORRECT:
```
"segment_indices_requested":[1,2]
"segments_with_audio":[1,2]
```

Example WRONG:
```
"segment_indices_requested":[1,2]
"segments_with_audio":[0,1,2,3,4,5,6,7,8,9,10,11,12]  ← ALL segments!
```

## Next Steps (If Bug Confirmed)

1. Show me:
   - Screenshot of console logs
   - Laravel log lines with TTS Request
   - File listing screenshot
   - Network request payload screenshot

2. We'll identify:
   - Is it frontend sending all segments?
   - Is it backend processing all segments?
   - Is it database saving all segments?

3. Fix the issue

## Questions?

If you need clarification on any step, let me know!
