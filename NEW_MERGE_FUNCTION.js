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

    // RULE 1: Check if text ends with connector words
    // These words indicate the sentence continues to the next segment
    const endsWithConnector = (text) => {
        const trimmed = text.toLowerCase().trim();
        const connectors = [
            // Conjunctions - connect clauses
            'and', 'or', 'but', 'yet', 'so', 'because', 'if', 'unless', 'while', 'when',
            'after', 'before', 'since', 'than', 'nor',
            // Prepositions - show relationships
            'of', 'in', 'on', 'at', 'to', 'for', 'with', 'from', 'by', 'about', 'into',
            'through', 'during', 'between', 'among', 'within', 'without', 'under', 'over',
            'above', 'below', 'around', 'along', 'across', 'toward',
            // Articles/verbs that indicate continuation
            'the', 'a', 'an', 'as', 'is', 'are', 'was', 'were', 'be', 'been', 'being'
        ];
        const lastWord = trimmed.split(/\s+/).pop() || '';
        return connectors.includes(lastWord);
    };

    // Main decision logic for segment splitting
    const shouldForceSplit = (text, wordCount) => {
        // RULE 1: Never split if ends with connector
        // Example: "According to" → continues to "According to research"
        if (endsWithConnector(text)) {
            return false;
        }

        // RULE 2: Never split if no sentence-ending punctuation
        // Incomplete sentence - must continue to complete the thought
        // Example: "As you may know" (no .) → continues
        if (!hasSentenceEnd(text)) {
            return false;
        }

        // RULE 3: Split if proper punctuation AND no connector AND reasonable length
        // At this point we know: has punctuation AND doesn't end with connector
        return wordCount >= 20; // Lower threshold since we have valid ending
    };

    // Process each segment
    segments.forEach((segment, index) => {
        const text = normalizeText(segment.original_text || segment.text || '');
        if (!text) return; // Skip empty segments

        if (!bufferText) {
            startTime = segment.start ?? segment.start_time ?? 0;
        }

        bufferText += (bufferText ? ' ' : '') + text;
        duration += getDuration(segment);

        const wordCount = bufferText.split(/\s+/).filter(Boolean).length;
        const isLastSegment = index === segments.length - 1;

        let shouldCreateSegment = false;

        if (isLastSegment) {
            // Always create segment for last item
            shouldCreateSegment = true;
        } else if (shouldForceSplit(bufferText, wordCount)) {
            // Passed all rules: proper punctuation + no connector + good length
            shouldCreateSegment = true;
        } else if (wordCount >= 50) {
            // RULE 4: Safety overflow - prevent buffer from getting too large
            shouldCreateSegment = true;
        }
        // Otherwise: continue merging to next segment

        if (shouldCreateSegment && bufferText.trim()) {
            const normalized = normalizeText(bufferText);
            merged.push({
                index: merged.length,
                text: normalized,
                original_text: normalized,
                start: startTime || 0,
                start_time: startTime || 0,
                end_time: (startTime || 0) + duration,
                duration: duration
            });

            bufferText = '';
            duration = 0;
        }
    });

    return merged;
}
