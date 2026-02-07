// Improved mergeSegmentsIntoSentences - COPY THIS FUNCTION INTO dubsync.js

function mergeSegmentsIntoSentences(segments) {
    const merged = [];
    let bufferText = '';
    let startTime = null;
    let duration = 0;

    // Helper functions
    const normalizeText = (text) => (text || '').replace(/\s+/g, ' ').trim();
    const getDuration = (segment) => {
        if (segment.duration && segment.duration > 0) return segment.duration;
        const start = segment.start ?? segment.start_time ?? 0;
        const end = segment.end_time ?? 0;
        if (end && end > start) return end - start;
        return 0;
    };

    // Check if text ends with sentence-ending punctuation (. ! ? …)
    const hasSentenceEnd = (text) => /[.!?…]+["')\]]?\s*$/.test(text);

    // Check if text ends with connector words (prepositions/conjunctions)
    // These words indicate the sentence continues to the next segment
    const endsWithConnector = (text) => {
        const trimmed = text.toLowerCase().trim();
        const connectors = [
            // Conjunctions - connect clauses/phrases
            'and', 'or', 'but', 'yet', 'so', 'because', 'if', 'unless', 'while', 'when', 
            'after', 'before', 'since', 'than', 'nor',
            // Prepositions - show relationships/direction
            'of', 'in', 'on', 'at', 'to', 'for', 'with', 'from', 'by', 'about', 'into', 
            'through', 'during', 'between', 'among', 'within', 'without', 'under', 'over', 
            'above', 'below', 'before', 'after', 'around', 'along', 'across', 'toward',
            // Articles and determiners
            'the', 'a', 'an', 'as', 'is', 'are', 'was', 'were', 'be', 'been', 'being'\n        ];\n        const lastWord = trimmed.split(/\\s+/).pop() || '';\n        return connectors.includes(lastWord);\n    };\n\n    // Main segmentation logic\n    const shouldForceSplit = (text, wordCount) => {\n        // RULE 1: Never split if ends with connector\n        // Example: \"According to\" continues to \"According to research\"\n        if (endsWithConnector(text)) {\n            return false;\n        }\n\n        // RULE 2: Never split if no sentence-ending punctuation\n        // Incomplete sentence - must continue\n        // Example: \"As you know\" has no . so it continues\n        if (!hasSentenceEnd(text)) {\n            return false;\n        }\n\n        // RULE 3: Split only if complete sentence (Rule 2 passed)\n        // AND has reasonable length (20+ words)\n        return wordCount >= 20;\n    };\n\n    // Process each segment\n    segments.forEach((segment, index) => {\n        const text = normalizeText(segment.original_text || segment.text || '');\n        if (!text) return; // Skip empty\n\n        if (!bufferText) {\n            startTime = segment.start ?? segment.start_time ?? 0;\n        }\n\n        bufferText += (bufferText ? ' ' : '') + text;\n        duration += getDuration(segment);\n\n        const wordCount = bufferText.split(/\\s+/).filter(Boolean).length;\n        const isLastSegment = index === segments.length - 1;\n\n        let shouldCreateSegment = false;\n\n        if (isLastSegment) {\n            // Always finalize at end\n            shouldCreateSegment = true;\n        } else if (shouldForceSplit(bufferText, wordCount)) {\n            // Valid: proper punctuation + no connector + good length\n            shouldCreateSegment = true;\n        } else if (wordCount >= 50) {\n            // Safety: prevent buffer overflow\n            shouldCreateSegment = true;\n        }\n        // Otherwise: continue to next segment\n\n        if (shouldCreateSegment && bufferText.trim()) {\n            const normalized = normalizeText(bufferText);\n            merged.push({\n                index: merged.length,\n                text: normalized,\n                original_text: normalized,\n                start: startTime || 0,\n                start_time: startTime || 0,\n                end_time: (startTime || 0) + duration,\n                duration: duration\n            });\n\n            bufferText = '';\n            duration = 0;\n        }\n    });\n\n    return merged;\n}\n