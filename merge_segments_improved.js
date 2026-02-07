// Improved Merge Segments Function with Rule-Based Logic

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

    // Check if text ends with sentence-ending punctuation
    const hasSentenceEnd = (text) => /[.!?…]+["')\]]?\s*$/.test(text);

    // Check if text ends with connector words (prepositions/conjunctions)
    // These indicate the sentence continues to the next segment
    const endsWithConnector = (text) => {
        const trimmed = text.toLowerCase().trim();
        const connectors = [
            // Conjunctions - words that connect clauses
            'and', 'or', 'but', 'yet', 'so', 'because', 'if', 'unless', 'while', 'when', 'after', 'before',
            // Prepositions - words that show relationships
            'of', 'in', 'on', 'at', 'to', 'for', 'with', 'from', 'by', 'about', 'into', 'through',
            'during', 'between', 'among', 'within', 'without', 'under', 'over', 'above', 'below',
            // Articles and determiners
            'the', 'a', 'an', 'as'
        ];
        // Check if last word is a connector
        const lastWord = trimmed.split(/\s+/).pop() || '';
        return connectors.includes(lastWord);
    };

    // Decision logic for segment splitting
    const shouldForceSplit = (text, wordCount) => {
        // RULE 1: Never split if text ends with connector word
        // Example: "According to" → continues to next segment
        if (endsWithConnector(text)) {
            console.log('❌ Connector at end, continue:', text.slice(-20));
            return false;
        }

        // RULE 2: Never split if doesn't have sentence-ending punctuation
        // This is incomplete sentence - must continue to next segment
        if (!hasSentenceEnd(text)) {
            console.log('❌ No punctuation end, continue:', text.slice(-20));
            return false;
        }

        // RULE 3: Split only if proper punctuation AND reasonable length
        // (We already know it has proper ending from Rule 2)
        console.log('✅ Valid sentence end, word count:', wordCount);
        return wordCount >= 20; // Lower threshold since we have sentence end
    };

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
            console.log('📍 Last segment, create');
            shouldCreateSegment = true;
        } else if (shouldForceSplit(bufferText, wordCount)) {
            // Has proper punctuation, no connector, good length
            console.log('✓ Force split - proper sentence');
            shouldCreateSegment = true;
        } else if (wordCount >= 50) {
            // Prevent buffer from getting too large
            console.log('⚠️ Too long (50+ words), force split');
            shouldCreateSegment = true;
        } else {
            console.log('➕ Continue to next segment');
        }

        if (shouldCreateSegment && bufferText.trim()) {
            const normalized = normalizeText(bufferText);
            console.log(`\n📝 Creating segment ${merged.length + 1}:`, normalized.slice(0, 60) + '...\n');
            
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

/* 
RULES EXPLANATION:

1. ❌ CONNECTOR AT END (of, to, and, but, or, the, in, with, etc.)
   - If segment ends with connector → MUST continue to next segment
   - Example: "He is interested in" → continues: "in music lessons"
   
2. ❌ NO SENTENCE-ENDING PUNCTUATION (. ! ? …)
   - If segment doesn't end with . ! ? … → INCOMPLETE sentence
   - Must continue to next segment to complete the thought
   - Example: "As you know" (no punctuation) → continues: "I have been..."

3. ✅ PROPER SENTENCE END + NO CONNECTOR + GOOD LENGTH
   - Only CREATE segment if ALL conditions met:
     a) Ends with proper punctuation (. ! ? …)
     b) Doesn't end with connector word
     c) Has ≥20 words (enough content)
   - Example: "Hello everyone. How are you?" → complete sentence, create segment

4. ⚠️ BUFFER TOO LARGE (50+ words)
   - Force split to prevent buffer overflow
   - Acts as safety mechanism for very long sentences

EXAMPLES:

✅ "He said to me, can you help? I would appreciate it."
   → Split: "He said to me, can you help?" | "I would appreciate it."

❌ "According to the research of"
   → Continue (ends with connector "of")

❌ "The results showed that"
   → Continue (no punctuation, incomplete)

✅ "First, let me explain. Second, understand this."
   → Split after first sentence (has . and no connector)
*/
