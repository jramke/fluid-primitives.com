/**
 * Wraps a hydration call with performance.mark/measure and writes the result into the
 * #bench-result DOM node the BenchmarkMiddleware-served pages render - a cheap timing signal
 * readable via DevTools/console now, and a hook a future automated (e.g. Playwright) reader
 * could pick up without committing to that dependency in this v1.
 */
export function withTiming(name: string, fn: () => void): void {
    const startMark = `bench:${name}:start`;
    const endMark = `bench:${name}:end`;

    performance.mark(startMark);
    fn();
    performance.mark(endMark);

    const measure = performance.measure(`bench:${name}`, startMark, endMark);
    const durationMs = measure.duration;

    console.log(`[benchmarks] ${name} hydration took ${durationMs.toFixed(3)}ms`);

    const resultEl = document.getElementById('bench-result');
    if (resultEl instanceof HTMLElement) {
        resultEl.dataset.duration = String(durationMs);
        resultEl.dataset.component = name;
    }
}
