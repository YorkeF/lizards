import type { Lizard, TreeNode } from '../types';

function buildNode(
  name: string,
  nameMap: Map<string, Lizard>,
  visited: Set<string>,
): TreeNode {
  const normalized = name.trim();
  const lizard = nameMap.get(normalized.toLowerCase()) ?? null;

  if (visited.has(normalized.toLowerCase())) {
    // Cycle detected — return leaf node
    return { lizard, name: normalized, sire: null, dame: null };
  }

  visited.add(normalized.toLowerCase());

  const sireName = lizard?.sire ?? '';
  const dameName = lizard?.dame ?? '';

  return {
    lizard,
    name: normalized,
    sire: sireName ? buildNode(sireName, nameMap, new Set(visited)) : null,
    dame: dameName ? buildNode(dameName, nameMap, new Set(visited)) : null,
  };
}

export function buildTreeForest(lizards: Lizard[]): TreeNode[] {
  const nameMap = new Map<string, Lizard>();
  for (const l of lizards) {
    nameMap.set(l.name.toLowerCase(), l);
  }

  // Roots: lizards whose Sire and Dame are either empty or not in the dataset
  const roots = lizards.filter(l => {
    const sireKnown = l.sire && nameMap.has(l.sire.toLowerCase());
    const dameKnown = l.dame && nameMap.has(l.dame.toLowerCase());
    return !sireKnown && !dameKnown;
  });

  return roots.map(l => buildNode(l.name, nameMap, new Set()));
}

export function buildAncestryTree(lizard: Lizard, allLizards: Lizard[]): TreeNode {
  const nameMap = new Map<string, Lizard>();
  for (const l of allLizards) {
    nameMap.set(l.name.toLowerCase(), l);
  }
  return buildNode(lizard.name, nameMap, new Set());
}
