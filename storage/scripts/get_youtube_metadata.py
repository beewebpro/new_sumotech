#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
YouTube Metadata Fetcher
Fetches title, description, duration, and thumbnail from YouTube videos
"""

import sys
import io
import json
import os
from pathlib import Path
from urllib.request import urlopen
from urllib.parse import urlencode
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
        print(message, file=sys.stderr)

def extract_video_id(url):
    """Extract video ID from various YouTube URL formats"""
    patterns = [
        r'(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)',
        r'youtube\.com\/embed\/([^&\n?#]+)',
    ]
    
    for pattern in patterns:
        match = re.search(pattern, url)
        if match:
            return match.group(1)
    return None

def get_metadata_from_html(video_id):
    """
    Extract metadata from YouTube video page HTML
    This method works without API key
    """
    try:
        url = f"https://www.youtube.com/watch?v={video_id}"
        
        # Fetch the page with a user agent to avoid being blocked
        from urllib.request import Request
        req = Request(url, headers={
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
        
        with urlopen(req, timeout=10) as response:
            html_content = response.read().decode('utf-8')
        
        metadata = {
            'title': None,
            'description': None,
            'duration': None,
            'thumbnail': None
        }
        
        # Extract title from og:title meta tag
        title_match = re.search(r'<meta\s+property="og:title"\s+content="([^"]*)"', html_content)
        if title_match:
            metadata['title'] = title_match.group(1)
        
        # Extract description from og:description meta tag
        desc_match = re.search(r'<meta\s+property="og:description"\s+content="([^"]*)"', html_content)
        if desc_match:
            metadata['description'] = desc_match.group(1)
        
        # Extract thumbnail from og:image meta tag
        thumb_match = re.search(r'<meta\s+property="og:image"\s+content="([^"]*)"', html_content)
        if thumb_match:
            metadata['thumbnail'] = thumb_match.group(1)
        
        # Try to extract duration from videoDetails in initial data
        duration_match = re.search(r'"lengthSeconds":"(\d+)"', html_content)
        if duration_match:
            duration_seconds = int(duration_match.group(1))
            hours = duration_seconds // 3600
            minutes = (duration_seconds % 3600) // 60
            seconds = duration_seconds % 60
            
            if hours > 0:
                metadata['duration'] = f"{hours}:{minutes:02d}:{seconds:02d}"
            else:
                metadata['duration'] = f"{minutes}:{seconds:02d}"
        
        return metadata
        
    except Exception as e:
        raise Exception(f"Failed to fetch metadata from YouTube page: {str(e)}")

def get_metadata_from_oembed(video_id):
    """
    Fetch basic metadata from YouTube oEmbed (title, thumbnail)
    """
    try:
        url = f"https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={video_id}&format=json"
        with urlopen(url, timeout=10) as response:
            data = json.loads(response.read().decode('utf-8'))

        return {
            'title': data.get('title'),
            'description': None,
            'duration': None,
            'thumbnail': data.get('thumbnail_url')
        }
    except Exception as e:
        raise Exception(f"Failed to fetch metadata from oEmbed: {str(e)}")

def get_metadata(video_id_or_url):
    """
    Get metadata for a YouTube video
    
    Args:
        video_id_or_url: YouTube video ID or full URL
        
    Returns:
        JSON string of metadata (title, description, duration, thumbnail)
    """
    try:
        # Extract video ID if URL was provided
        video_id = video_id_or_url
        if video_id_or_url.startswith('http'):
            video_id = extract_video_id(video_id_or_url)
            if not video_id:
                raise Exception("Could not extract video ID from URL")
        
        debug_print(f"DEBUG: Fetching metadata for video: {video_id}")
        
        metadata = {
            'title': None,
            'description': None,
            'duration': None,
            'thumbnail': None
        }

        # Try oEmbed first for title/thumbnail
        try:
            oembed = get_metadata_from_oembed(video_id)
            metadata.update({k: v for k, v in oembed.items() if v})
        except Exception as e:
            debug_print(f"DEBUG: oEmbed failed: {str(e)}")

        # Try HTML parsing for description/duration and richer data
        try:
            html_meta = get_metadata_from_html(video_id)
            metadata.update({k: v for k, v in html_meta.items() if v})
        except Exception as e:
            debug_print(f"DEBUG: HTML metadata failed: {str(e)}")
        
        debug_print(f"DEBUG: Successfully fetched metadata")
        
        # Output as JSON
        json_output = json.dumps(metadata, ensure_ascii=False, indent=None)
        print(json_output)
        
    except Exception as e:
        # Return default metadata instead of failing hard
        error_data = {
            'title': None,
            'description': None,
            'duration': None,
            'thumbnail': None,
            'error': str(e),
            'video_id': video_id_or_url
        }
        print(json.dumps(error_data))
        sys.exit(0)

if __name__ == '__main__':
    if len(sys.argv) < 2:
        error = json.dumps({'error': 'Video ID or URL required'})
        print(error)
        sys.exit(1)
    
    video_id_or_url = sys.argv[1]
    debug_print(f"DEBUG: Python script started for: {video_id_or_url}")
    get_metadata(video_id_or_url)

