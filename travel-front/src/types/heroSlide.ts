export interface LocalizedText {
  en: string;
  bn?: string;
}

export interface HeroSlide {
  id: number;
  title: string;
  description: string;
  com_name: string | null;
  photo_text: string | null;
  author_name: string | null;
  location: string | null;
  photo_date: string | null;
  translations?: {
    title: LocalizedText;
    description: LocalizedText;
    com_name?: LocalizedText;
    cta_text?: LocalizedText;
    photo_text?: LocalizedText;
    location?: LocalizedText;
    author_name?: LocalizedText;
    photo_date?: LocalizedText;
  };
  image: string;
  cta_text: string | null;
  cta_url: string | null;
  order: number;
  is_active: boolean;
  created_at: string;
}

export interface HeroSlideFormData {
  title: LocalizedText;
  description: LocalizedText;
  com_name: LocalizedText;
  cta_text: LocalizedText;
  photo_text: LocalizedText;
  location: LocalizedText;
  author_name: LocalizedText;
  photo_date: LocalizedText;
  image: string;
  cta_url: string;
  order: number;
  is_active: boolean;
}

export function emptyHeroSlideForm(): HeroSlideFormData {
  return {
    title: { en: "", bn: "" },
    description: { en: "", bn: "" },
    com_name: { en: "", bn: "" },
    cta_text: { en: "", bn: "" },
    photo_text: { en: "", bn: "" },
    location: { en: "", bn: "" },
    author_name: { en: "", bn: "" },
    photo_date: { en: "", bn: "" },
    image: "",
    cta_url: "",
    order: 0,
    is_active: true,
  };
}