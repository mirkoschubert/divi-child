// WordPress-spezifische Typen
export interface DiviChildConfig {
  apiUrl: string;
  nonce: string;
  version: string;
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
  type: 'text' | 'textarea' | 'toggle' | 'select' | 'multi_select' | 'number' | 'list' | 'repeater' | 'font_selector' | 'color' | 'group';
  label?: string;
  title?: string;
  description?: string;
  default?: unknown;
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

// Global WordPress Objekte
declare global {
  interface Window {
    diviChildConfig?: DiviChildConfig;
    wp: {
      element: typeof import('@wordpress/element');
      components: typeof import('@wordpress/components');
      apiFetch: typeof import('@wordpress/api-fetch').default;
      i18n: typeof import('@wordpress/i18n');
    };
  }
}

export {};
