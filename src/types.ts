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
}

export interface TreeNode {
  lizard: Lizard | null; // null = referenced but not in CSV ("ghost" node)
  name: string;
  sire: TreeNode | null;
  dame: TreeNode | null;
}

// Raw row as PapaParse sees it from the CSV header names
export interface RawLizardRow {
  Name: string;
  Species: string;
  Morph: string;
  Locality: string;
  Gender: string;
  'Date of Birth': string;
  Sire: string;
  Dame: string;
  'Weight (g)': string;
  Price: string;
  Available: string;
  Description: string;
  Photo: string;
  'Obtained From': string;
}
