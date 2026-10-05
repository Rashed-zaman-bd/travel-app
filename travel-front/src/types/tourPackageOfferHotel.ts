export interface LocalizedString {
  en: string;
  bn?: string | null;
}

export interface TourPackageOfferHotel {
  id: number;
  tour_package_id: number;
  tour_package_offer_id: number;
  hotel_name: LocalizedString;
  location?: LocalizedString | null;
  order: number;
  is_active: boolean;
  created_at?: string;
  updated_at?: string;
  tour_package?: { id: number; title?: string | LocalizedString };
  tour_package_offer?: { id: number; title?: string | LocalizedString };
}

export interface TourPackageOfferHotelPayload {
  tour_package_id: number | null;
  tour_package_offer_id: number | null;
  hotel_name: LocalizedString;
  location: LocalizedString;
  order: number;
  is_active: boolean;
}