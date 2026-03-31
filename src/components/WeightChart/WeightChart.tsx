import type { WeightEntry } from '../../types';
import styles from './WeightChart.module.css';

interface Props {
  entries: WeightEntry[]; // sorted ascending by date
}

const W = 480;
const H = 200;
const PAD = { top: 16, right: 24, bottom: 48, left: 48 };
const INNER_W = W - PAD.left - PAD.right;
const INNER_H = H - PAD.top - PAD.bottom;

function formatDate(iso: string): string {
  const d = new Date(iso + 'T00:00:00');
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: '2-digit' });
}

export function WeightChart({ entries }: Props) {
  if (entries.length < 2) return null;

  const timestamps = entries.map(e => new Date(e.date + 'T00:00:00').getTime());
  const weights    = entries.map(e => e.weightG);

  const minT = Math.min(...timestamps);
  const maxT = Math.max(...timestamps);
  const minW = Math.min(...weights);
  const maxW = Math.max(...weights);

  // Add vertical padding so line doesn't hug top/bottom
  const wPad  = (maxW - minW) * 0.12 || 1;
  const wLow  = minW - wPad;
  const wHigh = maxW + wPad;
  const wRange = wHigh - wLow;
  const tRange = maxT - minT || 1;

  function toX(t: number): number {
    return PAD.left + ((t - minT) / tRange) * INNER_W;
  }
  function toY(w: number): number {
    return PAD.top + (1 - (w - wLow) / wRange) * INNER_H;
  }

  const points = entries.map(e => ({
    x: toX(new Date(e.date + 'T00:00:00').getTime()),
    y: toY(e.weightG),
    entry: e,
  }));

  const polyline = points.map(p => `${p.x},${p.y}`).join(' ');

  // Y-axis ticks: 4 evenly spaced
  const yTicks = [0, 1, 2, 3].map(i => {
    const w = wLow + (wRange / 3) * i;
    return { y: toY(w), label: `${Math.round(w * 10) / 10}g` };
  });

  // X-axis ticks: first, last, and up to 2 middle points
  const xTickIndices = Array.from(
    new Set([
      0,
      Math.floor(entries.length / 3),
      Math.floor((entries.length * 2) / 3),
      entries.length - 1,
    ]),
  ).filter(i => i >= 0 && i < entries.length);

  return (
    <svg
      viewBox={`0 0 ${W} ${H}`}
      className={styles.chart}
      role="img"
      aria-label="Weight over time chart"
    >
      {/* Y-axis gridlines + labels */}
      {yTicks.map((tick, i) => (
        <g key={i}>
          <line
            x1={PAD.left} y1={tick.y}
            x2={W - PAD.right} y2={tick.y}
            className={styles.grid}
          />
          <text x={PAD.left - 6} y={tick.y} className={styles.axisLabel} textAnchor="end" dominantBaseline="middle">
            {tick.label}
          </text>
        </g>
      ))}

      {/* X-axis tick labels */}
      {xTickIndices.map(i => (
        <text
          key={i}
          x={points[i].x}
          y={H - PAD.bottom + 14}
          className={styles.axisLabel}
          textAnchor="middle"
        >
          {formatDate(entries[i].date)}
        </text>
      ))}

      {/* Line */}
      <polyline points={polyline} className={styles.line} />

      {/* Data points */}
      {points.map((p, i) => (
        <circle key={i} cx={p.x} cy={p.y} r={4} className={styles.dot}>
          <title>{formatDate(p.entry.date)}: {p.entry.weightG}g</title>
        </circle>
      ))}
    </svg>
  );
}
