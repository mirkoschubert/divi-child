import { useState, useEffect, useCallback } from '@wordpress/element'
import apiFetch from '@wordpress/api-fetch'
import { __ } from '@wordpress/i18n'

import type {
  UseModulesReturn,
  ModulesApiResponse,
  ApiResponse,
  ModuleInfo,
} from '@/types'

const isDebug = !!window.diviChildConfig?.debug

export const useModules = (): UseModulesReturn => {
  const [modules, setModules] = useState<Record<string, ModuleInfo>>({})
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const fetchModules = useCallback(async () => {
    try {
      setIsLoading(true)
      setError(null)

      if (isDebug) console.log('DiviChild: Config:', window.diviChildConfig)
      if (!window.diviChildConfig) {
        throw new Error('diviChildConfig is not defined')
      }

      const apiUrl = `${window.location.origin}/wp-json/divi-child/v1/modules`

      const restNonce = window.diviChildConfig?.nonce || ''

      if (!restNonce) {
        throw new Error('No REST API nonce available')
      }

      if (isDebug) console.log('DiviChild: Using nonce:', restNonce.substring(0, 10) + '...')

      const response = await fetch(apiUrl, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': restNonce
        }
      })

      if (isDebug) console.log('DiviChild: Response status:', response.status)

      if (!response.ok) {
        const errorText = await response.text()
        if (isDebug) console.error('DiviChild: API error response:', errorText)
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }

      const data = await response.json() as ModulesApiResponse

      if (data.success && data.data) {
        const processedModules: Record<string, ModuleInfo> = {}

        Object.entries(data.data).forEach(([slug, moduleData]) => {
          const module = moduleData as ModuleInfo
          processedModules[slug] = module
        })

        setModules(processedModules)

        if (isDebug) {
          console.log('DiviChild: Loaded modules:', Object.keys(processedModules))
        }
      } else {
        setError('Failed to load modules - invalid response')
      }
    } catch (err) {
      if (isDebug) console.error('DiviChild: API error:', err)
      setError(err instanceof Error ? err.message : 'Error loading modules')
    } finally {
      setIsLoading(false)
    }
  }, [])

  useEffect(() => {
    fetchModules()
  }, [fetchModules])

  const toggleModule = useCallback(
    async (moduleSlug: string, enabled: boolean): Promise<ApiResponse> => {
      try {
        if (isDebug) console.log(`DiviChild: Toggling ${moduleSlug}:`, { enabled })

        const response = await apiFetch<ApiResponse>({
          path: `/divi-child/v1/modules/${moduleSlug}`,
          method: 'POST',
          data: { enabled },
        })

        if (response.success) {
          setModules((prev) => ({
            ...prev,
            [moduleSlug]: {
              ...prev[moduleSlug],
              enabled,
            },
          }))
          if (isDebug) console.log(`DiviChild: ${moduleSlug} toggled successfully`)
        }

        return response
      } catch (err) {
        if (isDebug) console.error(`DiviChild: Error toggling ${moduleSlug}:`, err)
        throw err
      }
    },
    []
  )

  const updateModuleSettings = useCallback(
    async (
      moduleSlug: string,
      settings: Record<string, unknown>
    ): Promise<ApiResponse> => {
      try {
        if (isDebug) console.log(`DiviChild: Updating settings for ${moduleSlug}:`, settings)

        const response = await apiFetch<ApiResponse>({
          path: `/divi-child/v1/modules/${moduleSlug}/settings`,
          method: 'POST',
          data: settings,
        })

        if (response.success) {
          setModules((prev) => ({
            ...prev,
            [moduleSlug]: {
              ...prev[moduleSlug],
              options: {
                ...prev[moduleSlug].options,
                ...settings,
              },
            },
          }))
          if (isDebug) console.log(`DiviChild: Settings for ${moduleSlug} updated`)
        }

        return response
      } catch (err) {
        if (isDebug) console.error(`DiviChild: Error updating ${moduleSlug}:`, err)
        throw err
      }
    },
    []
  )

  return {
    modules,
    isLoading,
    error,
    toggleModule,
    updateModuleSettings,
    reload: fetchModules,
  }
}
