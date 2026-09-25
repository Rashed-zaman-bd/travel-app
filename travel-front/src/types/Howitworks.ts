import api from "@/services/api";

export interface LocalizedText {
  [locale: string]: string;
}

export interface HowItWorksStep {
  id: number;
  heading: string;
  topline: string | null;
  title: string;
  description: string | null;
  translations?: {
    heading: LocalizedText;
    topline: LocalizedText;
    title: LocalizedText;
    description: LocalizedText;
  };
  icon: [string, string]; // [prefix, name] e.g. ["fas", "plane"]
  order: number;
  is_active: boolean;
  created_at: string;
}

export interface HowItWorksStepPayload {
  heading: LocalizedText;
  topline?: LocalizedText;
  title: LocalizedText;
  description?: LocalizedText;
  icon: string; // "fas:plane"
  order?: number;
  is_active?: boolean;
}

export const howItWorksApi = {
  /** Public endpoint — GET /how-it-works, no auth needed, active steps only. */
  list() {
    return api.get<{ data: HowItWorksStep[] }>("/how-it-works");
  },

  adminList() {
    return api.get<{ data: HowItWorksStep[] }>("/admin/how-it-works");
  },

  show(id: number) {
    return api.get<{ data: HowItWorksStep }>(`/admin/how-it-works/${id}`);
  },

  create(payload: HowItWorksStepPayload) {
    return api.post<{ data: HowItWorksStep }>("/admin/how-it-works", payload);
  },

  update(id: number, payload: Partial<HowItWorksStepPayload>) {
    return api.patch<{ data: HowItWorksStep }>(`/admin/how-it-works/${id}`, payload);
  },

  destroy(id: number) {
    return api.delete(`/admin/how-it-works/${id}`);
  },

  reorder(orderedIds: number[]) {
    return api.post("/admin/how-it-works/reorder", { order: orderedIds });
  },
};