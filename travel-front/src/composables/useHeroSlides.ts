import { ref } from "vue";
import api from "@/services/api";
import type { HeroSlide, HeroSlideFormData } from "@/types/heroSlide";

function buildFormData(payload: HeroSlideFormData, imageFile: File | null): FormData {
  const form = new FormData();

  // Translatable fields — flatten to title[en], title[bn], etc.
  const localizedFields: (keyof HeroSlideFormData)[] = [
    "title",
    "description",
    "com_name",
    "cta_text",
    "photo_text",
    "location",
    "author_name",
    "photo_date",
  ];

  for (const field of localizedFields) {
    const value = payload[field] as { en: string; bn?: string };
    form.append(`${field}[en]`, value.en ?? "");
    if (value.bn) form.append(`${field}[bn]`, value.bn);
  }

  // Non-translatable fields
  if (payload.cta_url) form.append("cta_url", payload.cta_url);
  form.append("order", String(payload.order ?? 0));
  form.append("is_active", payload.is_active ? "1" : "0");

  // Image — only attach if a new file was picked
  if (imageFile) {
    form.append("image", imageFile);
  }

  return form;
}

export function useHeroSlides() {
  const slides = ref<HeroSlide[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function fetchAll() {
    loading.value = true;
    error.value = null;
    try {
      const { data } = await api.get("/admin/hero-slides");
      slides.value = data.data;
    } catch (e: any) {
      error.value = e?.response?.data?.message || "Failed to load hero slides.";
    } finally {
      loading.value = false;
    }
  }

  async function create(payload: HeroSlideFormData, imageFile: File) {
    const form = buildFormData(payload, imageFile);
    const { data } = await api.post("/admin/hero-slides", form, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    slides.value.push(data.data);
    slides.value.sort((a, b) => a.order - b.order);
    return data.data;
  }

  async function update(id: number, payload: HeroSlideFormData, imageFile: File | null) {
    const form = buildFormData(payload, imageFile);
    form.append("_method", "PUT"); // Laravel MethodOverride: PHP can't reliably parse multipart on a real PUT

    const { data } = await api.post(`/admin/hero-slides/${id}`, form, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    const idx = slides.value.findIndex((s) => s.id === id);
    if (idx !== -1) slides.value[idx] = data.data;
    return data.data;
  }

  async function remove(id: number) {
    await api.delete(`/admin/hero-slides/${id}`);
    slides.value = slides.value.filter((s) => s.id !== id);
  }

  return { slides, loading, error, fetchAll, create, update, remove };
}