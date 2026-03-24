import { useState } from 'react';
import { useLizards } from '../../hooks/useLizards';
import { buildTreeForest, buildAncestryTree } from '../../utils/genealogy';
import { TreeNode } from '../../components/TreeNode/TreeNode';
import styles from './FamilyTree.module.css';

export function FamilyTree() {
  const { lizards, loading, error } = useLizards();
  const [filter, setFilter] = useState('');

  if (loading) return <div className={styles.state}>Loading...</div>;
  if (error) return <div className={styles.state}>Error: {error}</div>;

  if (lizards.length === 0) {
    return (
      <main className={styles.main}>
        <h1>Family Tree</h1>
        <div className={styles.state}>No lizards in the records yet.</div>
      </main>
    );
  }

  const filtered = filter
    ? lizards.filter(l => l.name === filter)
    : null;

  const trees = filtered
    ? filtered.map(l => buildAncestryTree(l, lizards))
    : buildTreeForest(lizards);

  return (
    <main className={styles.main}>
      <h1>Family Tree</h1>
      <div className={styles.controls}>
        <label htmlFor="filter">View ancestry for:</label>
        <select
          id="filter"
          value={filter}
          onChange={e => setFilter(e.target.value)}
          className={styles.select}
        >
          <option value="">All family lines</option>
          {lizards.map(l => (
            <option key={l.slug} value={l.name}>{l.name}</option>
          ))}
        </select>
      </div>

      {trees.length === 0 ? (
        <div className={styles.state}>No family lines to display.</div>
      ) : (
        <div className={styles.forest}>
          {trees.map((tree, i) => (
            <div key={i} className={styles.treeWrap}>
              <TreeNode node={tree} />
            </div>
          ))}
        </div>
      )}
    </main>
  );
}
