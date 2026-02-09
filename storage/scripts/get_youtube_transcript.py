#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
YouTube Transcript Fetcher - Using RapidAPI
Fetches transcript with timestamps from YouTube videos via RapidAPI
"""

import sys
import io
import json
import time
import http.client
import os
from pathlib import Path
import html
import re

# Force UTF-8 encoding for stdout/stderr on Windows
if sys.platform == 'win32':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
    sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

# Load environment variables from .env file
def load_env():
    """Load environment variables from .env file"""
    env_file = Path(__file__).parent.parent.parent / '.env'
    if env_file.exists():
        with open(env_file, 'r') as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith('#') and '=' in line:
                    key, value = line.split('=', 1)
                    os.environ[key.strip()] = value.strip().strip('"\'')

# Load environment variables
load_env()

# Debug mode - set to False to disable debug output
DEBUG_MODE = os.getenv('PYTHON_DEBUG', 'false').lower() == 'true'

def debug_print(message):
    """Print debug message only if DEBUG_MODE is enabled"""
    if DEBUG_MODE:
        print(message)

def contains_arabic(text):
    """Check if text contains Arabic characters"""
    if not text:
        return False
    return re.search(r'[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF]', text) is not None

# RapidAPI Configuration from environment variables
RAPIDAPI_HOST = os.getenv('RAPIDAPI_HOST', 'youtube-transcript3.p.rapidapi.com')
RAPIDAPI_KEY = os.getenv('RAPIDAPI_KEY')

# Validate API credentials
if not RAPIDAPI_KEY:
    print(json.dumps({'error': 'RAPIDAPI_KEY not set in environment variables'}))
    sys.exit(1)
if not RAPIDAPI_HOST:
    print(json.dumps({'error': 'RAPIDAPI_HOST not set in environment variables'}))
    sys.exit(1)
# Validate API key is set
if not RAPIDAPI_KEY:
    raise Exception("RAPIDAPI_KEY not found in environment variables. Please set it in .env file.")
if not RAPIDAPI_HOST:
    raise Exception("RAPIDAPI_HOST not found in environment variables. Please set it in .env file.")

def get_transcript_from_rapidapi(video_id):
    """
    Fetch transcript from RapidAPI
    Prioritizes English (en) transcript if available
    
    Args:
        video_id: YouTube video ID
        
    Returns:
        list: Formatted transcript with timing
    """
    try:
        # Set connection timeout to 30 seconds
        conn = http.client.HTTPSConnection(RAPIDAPI_HOST, timeout=30)
        
        headers = {
            'x-rapidapi-key': RAPIDAPI_KEY,
            'x-rapidapi-host': RAPIDAPI_HOST
        }
        
        # Make request to RapidAPI - get available transcripts
        # Note: RapidAPI will return available transcripts, we'll filter for English
        conn.request("GET", f"/api/transcript?videoId={video_id}", headers=headers)
        
        res = conn.getresponse()
        # Properly decode UTF-8 with error handling
        raw_data = res.read()
        # Try UTF-8 first, fallback to ISO-8859-1
        try:
            data = raw_data.decode("utf-8")
        except UnicodeDecodeError:
            print("DEBUG: UTF-8 decode failed, trying ISO-8859-1")
            data = raw_data.decode("iso-8859-1", errors='replace')
        
        # DEBUG: Print raw response
        debug_print(f"DEBUG: RapidAPI Status: {res.status}")
        debug_print(f"DEBUG: Requested language: English (en)")
        debug_print(f"DEBUG: RapidAPI Raw Response (first 500 chars): {data[:500]}")
        
        if res.status != 200:
            raise Exception(f"RapidAPI returned status {res.status}: {data}")
        
        # Parse response
        response_data = json.loads(data)
        
        # DEBUG: Print parsed response structure
        debug_print(f"DEBUG: Response type: {type(response_data)}")
        if isinstance(response_data, dict):
            debug_print(f"DEBUG: Response keys: {list(response_data.keys())}")
            # Check if response has language info
            if 'lang' in response_data:
                debug_print(f"DEBUG: Response language: {response_data.get('lang')}")
        elif isinstance(response_data, list) and len(response_data) > 0:
            debug_print(f"DEBUG: First item keys: {list(response_data[0].keys()) if isinstance(response_data[0], dict) else 'N/A'}")
            if isinstance(response_data[0], dict) and 'lang' in response_data[0]:
                debug_print(f"DEBUG: First item language: {response_data[0].get('lang')}")
        
        # Check if API returned success
        if isinstance(response_data, dict) and 'error' in response_data:
            raise Exception(f"RapidAPI error: {response_data.get('error')}")
        
        # Format response data
        formatted_transcript = []
        current_time = 0.0
        languages = set()  # Initialize as empty set to avoid "int is not iterable" error
        
        if isinstance(response_data, list):
            # If response is already a list of transcript items
            debug_print(f"DEBUG: Processing list response with {len(response_data)} items")
            
            # Check if all items have language codes - filter for English if mixed
            languages = set()
            for item in response_data:
                if isinstance(item, dict) and 'lang' in item:
                    languages.add(item['lang'])
            
            debug_print(f"DEBUG: Languages in response: {languages}")
            
            # Only accept English transcripts - reject if no English available
            if languages and 'en' not in languages:
                raise Exception(f"Chỉ hỗ trợ transcript tiếng Anh. Video này có: {', '.join(languages)}")
            
            # Filter for English only
            items_to_process = response_data
            if 'en' in languages:
                debug_print(f"DEBUG: Filtering for English only")
                items_to_process = [item for item in response_data if item.get('lang', 'en') == 'en']
                debug_print(f"DEBUG: Filtered to {len(items_to_process)} English items")
            
            # If no items after filtering, reject
            if not items_to_process:
                raise Exception("Không tìm thấy transcript tiếng Anh")
            
            for idx, item in enumerate(items_to_process):
                if isinstance(item, dict) and 'text' in item:
                    # Check language if available
                    lang = item.get('lang', 'unknown')
                    if idx == 0:
                        debug_print(f"DEBUG: First item language code: {lang}")
                    
                    # RapidAPI uses 'offset' instead of 'start'
                    start_time = float(item.get('offset', item.get('start', current_time)))
                    duration = float(item.get('duration', 3.0))
                    
                    # Decode HTML entities (&#39; -> ', &quot; -> ", etc.)
                    raw_text = item.get('text', '')
                    if raw_text is None:
                        raw_text = ''
                    if not isinstance(raw_text, str):
                        raw_text = str(raw_text)
                    text = html.unescape(raw_text).strip()
                    
                    # DEBUG: Print first few items
                    if idx < 3:
                        debug_print(f"DEBUG: Item {idx}: lang={lang}, text='{text[:50]}...', start={start_time}, duration={duration}")
                    
                    formatted_transcript.append({
                        'text': text,
                        'start': start_time,
                        'duration': duration
                    })
                    
                    # Track current time for next segment
                    current_time = max(current_time, start_time + duration)
        elif isinstance(response_data, dict):
            # If response is nested (e.g., under 'transcript' key)
            transcript_data = response_data.get('transcript', response_data.get('subtitles', []))
            debug_print(f"DEBUG: Processing nested response, found {len(transcript_data) if isinstance(transcript_data, list) else 0} items")
            if isinstance(transcript_data, list) and len(transcript_data) > 0:
                # Check if all items have language codes - filter for English if mixed
                languages = set()
                for item in transcript_data:
                    if isinstance(item, dict) and 'lang' in item:
                        languages.add(item['lang'])
                
                debug_print(f"DEBUG: Languages in nested response: {languages}")
                
                # Only accept English transcripts - reject if no English available
                if languages and 'en' not in languages:
                    raise Exception(f"Chỉ hỗ trợ transcript tiếng Anh. Video này có: {', '.join(languages)}")
                
                # Filter for English only
                items_to_process = transcript_data
                if 'en' in languages:
                    debug_print(f"DEBUG: Filtering for English only")
                    items_to_process = [item for item in transcript_data if item.get('lang', 'en') == 'en']
                    debug_print(f"DEBUG: Filtered to {len(items_to_process)} English items")
                
                # If no items after filtering, reject
                if not items_to_process:
                    raise Exception("Không tìm thấy transcript tiếng Anh")
                
                for idx, item in enumerate(items_to_process):
                    if isinstance(item, dict) and 'text' in item:
                        # Check language if available
                        lang = item.get('lang', 'unknown')
                        if idx == 0:
                            debug_print(f"DEBUG: First item language code: {lang}")
                        
                        # RapidAPI uses 'offset' instead of 'start'
                        start_time = float(item.get('offset', item.get('start', current_time)))
                        duration = float(item.get('duration', 3.0))
                        
                        # Decode HTML entities (&#39; -> ', &quot; -> ", etc.)
                        raw_text = item.get('text', '')
                        if raw_text is None:
                            raw_text = ''
                        if not isinstance(raw_text, str):
                            raw_text = str(raw_text)
                        text = html.unescape(raw_text).strip()
                        
                        # DEBUG: Print first few items
                        if idx < 3:
                            debug_print(f"DEBUG: Item {idx}: lang={lang}, text='{text[:50]}...', start={start_time}, duration={duration}")
                        
                        formatted_transcript.append({
                            'text': text,
                            'start': start_time,
                            'duration': duration
                        })
                        
                        # Track current time for next segment
                        current_time = max(current_time, start_time + duration)
            else:
                debug_print(f"DEBUG: Response is dict but no valid transcript_data found")
                raise Exception(f"Unexpected response format from RapidAPI - found dict but no transcript data. Keys: {list(response_data.keys())}")
        
        if not formatted_transcript:
            raise Exception("No transcript data found in API response")

        # If language info is missing, enforce English by rejecting Arabic script
        # Only do this if languages set is not initialized (meaning we didn't check language codes)
        if isinstance(languages, set) and len(languages) == 0:
            sample_text = " ".join([item.get('text', '') for item in formatted_transcript[:10]])
            if contains_arabic(sample_text):
                raise Exception("Chỉ hỗ trợ transcript tiếng Anh. Transcript hiện tại có ký tự Ả Rập")
        
        debug_print(f"DEBUG: Total segments formatted: {len(formatted_transcript)}")
        debug_print(f"DEBUG: First segment: {formatted_transcript[0] if formatted_transcript else 'None'}")
        debug_print(f"DEBUG: Last segment: {formatted_transcript[-1] if formatted_transcript else 'None'}")
        
        return formatted_transcript
        
    except Exception as e:
        raise Exception(f"RapidAPI fetch failed: {str(e)}")

def get_transcript(video_id, timeout=50):
    """
    Fetch transcript for a YouTube video using RapidAPI
    
    Args:
        video_id: YouTube video ID
        timeout: Maximum time to wait (seconds)
        
    Returns:
        JSON string of transcript with timestamps
    """
    start_time = time.time()
    
    try:
        debug_print(f"DEBUG: Starting to fetch transcript for video: {video_id} using RapidAPI")
        
        # Fetch from RapidAPI
        formatted_transcript = get_transcript_from_rapidapi(video_id)
        
        elapsed = time.time() - start_time
        debug_print(f"DEBUG: Fetched {len(formatted_transcript)} segments in {elapsed:.2f}s from RapidAPI")
        
        # Normalize unicode characters before output
        import unicodedata
        normalized_transcript = []
        for item in formatted_transcript:
            normalized_item = {}
            for key, value in item.items():
                if isinstance(value, str):
                    # Normalize to NFC form (standard canonical form)
                    normalized_value = unicodedata.normalize('NFC', value)
                    normalized_item[key] = normalized_value
                else:
                    normalized_item[key] = value
            normalized_transcript.append(normalized_item)
        
        # Output as JSON with proper encoding
        # Ensure all output is UTF-8 encoded and safe
        json_output = json.dumps(normalized_transcript, ensure_ascii=False, indent=None)
        print(json_output)
        
    except Exception as e:
        error_data = {
            'error': str(e),
            'video_id': video_id,
            'elapsed_time': time.time() - start_time
        }
        print(json.dumps(error_data))
        sys.exit(1)

if __name__ == '__main__':
    if len(sys.argv) < 2:
        error = json.dumps({'error': 'Video ID required'})
        print(error)
        sys.exit(1)
    
    video_id = sys.argv[1]
    debug_print(f"DEBUG: Python script started for video {video_id} using RapidAPI")
    get_transcript(video_id)

