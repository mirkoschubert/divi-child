// WordPress-spezifische Typen
export interface DiviChildConfig {
  apiUrl: string;
  nonce: string;
  version: string;
  debug?: boolean;
  modules: Record<string, ModuleInfo>;
}

export interface ModuleInfo {
  slug: string;
  name: string;
  description: string;
  author?: string;
  version: string;
  enabled: boolean;
  options: Record<string, unknown>;
  admin_settings: AdminSettings;
}

export interface AdminSettings {
  [key: string]: FieldConfig;
}

export interface FieldConfig {
  type: 'text' | 'textarea' | 'toggle' | 'select' | 'multi_select' | 'number' | 'list' | 'repeater' | 'font_selector' | 'color' | 'group' | 'image';
  label?: string;
  title?: string;
  description?: string;
  default?: unknown;
  multi?: boolean; // For image field: allow multiple selection
  options?: Record<string, string>; // Für select fields
  fields?: Record<string, FieldConfig>; // Für repeater fields und groups
  depends_on?: Record<string, string | boolean | number>;
  validate?: {
    pattern?: string;
    min?: number;
    max?: number;
    error_message?: string;
  };
  dependencies?: {
    wordpress?: string;
    divi?: string;
    plugins?: Record<string, string>;
  };
  dependency_status?: DependencyStatus;
}

export interface DependencyStatus {
  supported: boolean;
}

// API Response Typen
export interface ApiResponse<T = unknown> {
  success: boolean;
  data?: T;
  message?: string;
}

export interface ModulesApiResponse {
  [key: string]: ModuleInfo;
}

// Event-Typen für Umami
export interface UmamiEvent {
  id: string;
  name: string;
}

// Hook Return-Typen
export interface UseModulesReturn {
  modules: Record<string, ModuleInfo>;
  isLoading: boolean;
  error: string | null;
  toggleModule: (moduleSlug: string, enabled: boolean) => Promise<ApiResponse>;
  updateModuleSettings: (moduleSlug: string, settings: Record<string, unknown>) => Promise<ApiResponse>;
  reload: () => Promise<void>;
}

// WordPress Media Modal Types
export interface WpMediaOptions {
  title?: string;
  multiple?: boolean;
  library?: { type?: string };
  button?: { text?: string };
}

export interface WpMediaFrame {
  on: (event: string, callback: () => void) => WpMediaFrame;
  open: () => WpMediaFrame;
  state: () => {
    get: (key: string) => {
      first: () => WpMediaAttachment;
      toJSON: () => WpMediaAttachment[];
    };
  };
}

export interface WpMediaAttachment {
  id: number;
  url: string;
  filename: string;
  title: string;
  sizes?: {
    thumbnail?: { url: string };
    medium?: { url: string };
    full?: { url: string };
  };
}

// Global WordPress Objekte
declare global {
  interface Window {
    diviChildConfig?: DiviChildConfig;
    wp: {
      element: typeof import('@wordpress/element');
      components: typeof import('@wordpress/components');
      apiFetch: typeof import('@wordpress/api-fetch').default;
      i18n: typeof import('@wordpress/i18n');
      media: (options: WpMediaOptions) => WpMediaFrame;
    };
  }
}

export {};
