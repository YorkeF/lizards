export interface WeightEntry {
  date: string;    // ISO date string, e.g. "2024-06-01"
  weightG: number;
}

export interface Lizard {
  slug: string;
  name: string;
  species: string;
  morph: string;
  locality: string;
  gender: string;
  dateOfBirth: string;
  sire: string;
  dame: string;
  weightG: number | null;
  price: number | null;
  available: boolean;
  description: string;
  photos: string[];
  obtainedFrom: string;
  weightHistory: WeightEntry[];
  isBreeder: boolean;
  hideWeightHistory: boolean;
}

export interface TreeNode {
  lizard: Lizard | null; // null = referenced but not in CSV ("ghost" node)
  name: string;
  sire: TreeNode | null;
  dame: TreeNode | null;
}

