import { useState, useEffect, useCallback } from '@wordpress/element'
import apiFetch from '@wordpress/api-fetch'
import { __ } from '@wordpress/i18n'

import type {
  UseModulesReturn,
  ModulesApiResponse,
  ApiResponse,
  ModuleInfo,
} from '@/types'

export const useModules = (): UseModulesReturn => {
  const [modules, setModules] = useState<Record<string, ModuleInfo>>({})
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const fetchModules = useCallback(async () => {
    try {
      setIsLoading(true)
      setError(null)

      console.log('diviChildConfig:', window.diviChildConfig)
      if (!window.diviChildConfig) {
        throw new Error('diviChildConfig is not defined')
      }

      const apiUrl = `${window.location.origin}/wp-json/divi-child/v1/modules`

      const restNonce = window.diviChildConfig?.nonce || ''

      if (!restNonce) {
        throw new Error('No REST API nonce available')
      }

      console.log('Using REST NONCE:', restNonce ? restNonce.substring(0, 10) + '...' : 'MISSING')

      const response = await fetch(apiUrl, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': restNonce
        }
      })

      console.log('Response status:', response.status)

      if (!response.ok) {
        const errorText = await response.text()
        console.error('API Error Response:', errorText)
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }

      const data = await response.json() as ModulesApiResponse

      if (data.success && data.data) {
        // 🔧 Type-Safe Verarbeitung
        const processedModules: Record<string, ModuleInfo> = {}
        
        Object.entries(data.data).forEach(([slug, moduleData]) => {
          // Type assertion für moduleData
          const module = moduleData as ModuleInfo
          processedModules[slug] = module
        })

        setModules(processedModules)

        // 🔍 DEBUG: Detaillierte Analyse jedes Moduls
        console.group('🔍 Module Analysis')
        Object.entries(processedModules).forEach(([slug, module]) => {
          console.group(`📦 Module: ${slug}`)
          console.log('✅ All properties:', Object.keys(module))
          console.log('📖 Name:', module.name)
          console.log('👤 Author:', module.author)
          console.log('👤 Author type:', typeof module.author)
          console.log('👤 Has Author:', !!module.author)
          console.log('🔢 Version:', module.version)
          console.log('⚡ Enabled:', module.enabled)
          console.log('📄 Complete module object:', module)
          console.groupEnd()
        })
        console.groupEnd()
      } else {
        setError('Failed to load modules - invalid response')
      }
    } catch (err) {
      console.error('❌ API Error:', err)
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
        console.log(`🔄 Toggling module ${moduleSlug}:`, { enabled })

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
          console.log(`✅ Module ${moduleSlug} toggled successfully`)
        }

        return response
      } catch (err) {
        console.error(`❌ Error toggling module ${moduleSlug}:`, err)
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
        console.log(`🛠️ Updating settings for ${moduleSlug}:`, settings)

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
          console.log(`✅ Settings for ${moduleSlug} updated successfully`)
        }

        return response
      } catch (err) {
        console.error(`❌ Error updating settings for ${moduleSlug}:`, err)
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
