import { Link } from 'react-router-dom';
import type { TreeNode as TreeNodeType } from '../../types';
import styles from './TreeNode.module.css';

interface Props {
  node: TreeNodeType;
  depth?: number;
}

const MAX_DEPTH = 8;

export function TreeNode({ node, depth = 0 }: Props) {
  const hasParents = node.sire !== null || node.dame !== null;

  return (
    <div className={styles.wrap}>
      {hasParents && depth < MAX_DEPTH && (
        <div className={styles.parents}>
          <div className={styles.parentSlot}>
            {node.sire
              ? <TreeNode node={node.sire} depth={depth + 1} />
              : <div className={styles.unknown}>Unknown Sire</div>}
          </div>
          <div className={styles.parentSlot}>
            {node.dame
              ? <TreeNode node={node.dame} depth={depth + 1} />
              : <div className={styles.unknown}>Unknown Dame</div>}
          </div>
        </div>
      )}
      <div className={styles.connector} />
      <div className={node.lizard ? styles.node : styles.ghost}>
        {node.lizard
          ? <Link to={`/lizard/${node.lizard.slug}`}>{node.name}</Link>
          : <span>{node.name}</span>}
        {node.lizard?.morph && (
          <span className={styles.morph}>{node.lizard.morph}</span>
        )}
      </div>
    </div>
  );
}
