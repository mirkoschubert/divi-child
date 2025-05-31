import { useState } from '@wordpress/element'
import { Spinner } from '@wordpress/components'
import { __ } from '@wordpress/i18n'

import { ModuleGrid, SettingsModal } from './components'
import { useModules } from './hooks/useModules'
import type { ModuleInfo } from './types'
import './App.styl' // Stylus statt CSS

const App: React.FC = () => {
  const { modules, isLoading, error, toggleModule, updateModuleSettings } =
    useModules()
  const [selectedModule, setSelectedModule] = useState<string | null>(null)
  const [isModalOpen, setIsModalOpen] = useState<boolean>(false)

  const handleOpenSettings = (moduleSlug: string): void => {
    setSelectedModule(moduleSlug)
    setIsModalOpen(true)
  }

  const handleCloseModal = (): void => {
    setSelectedModule(null)
    setIsModalOpen(false)
  }

  const handleSaveSettings = async (
    settings: Record<string, unknown>
  ): Promise<void> => {
    if (selectedModule) {
      await updateModuleSettings(selectedModule, settings)
      handleCloseModal()
    }
  }

  if (isLoading) {
    return (
      <div className="dvc-react-loading">
        <Spinner />
        <p>{__('Loading modules...', 'divi-child')}</p>
      </div>
    )
  }

  if (error) {
    return (
      <div className="dvc-react-admin">
        <div className="dvc-react-header">
          <h1>
            {__('Divi Child Theme', 'divi-child')}
            <small>v{window.diviChildConfig?.version}</small>
          </h1>
        </div>
        <div style={{ padding: '20px 24px' }}>
          <div className="error">{error}</div>
        </div>
      </div>
    )
  }

  const selectedModuleData: ModuleInfo | undefined = selectedModule
    ? modules[selectedModule]
    : undefined

  return (
    <div className="dvc-react-admin">
      {/* Header - WordPress-Style aber ohne separate Card */}
      <div className="dvc-react-header">
        <h1>
          {__('Divi Child Theme', 'divi-child')}
          <small>v{window.diviChildConfig?.version}</small>
        </h1>
      </div>

      {/* Module Grid */}
      <ModuleGrid
        modules={modules}
        onToggleModule={toggleModule}
        onOpenSettings={handleOpenSettings}
      />

      {/* Settings Modal */}
      {isModalOpen && selectedModule && selectedModuleData && (
        <SettingsModal
          moduleSlug={selectedModule}
          module={selectedModuleData}
          onClose={handleCloseModal}
          onSave={handleSaveSettings}
        />
      )}
    </div>
  )
}

export default App
