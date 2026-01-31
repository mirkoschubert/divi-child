import { __ } from '@wordpress/i18n'
import ModuleCard from '../ModuleCard/ModuleCard'
import type { ModuleInfo } from '@/types'

interface ModuleGridProps {
  modules: Record<string, ModuleInfo>
  onToggleModule: (slug: string, enabled: boolean) => Promise<unknown>
  onOpenSettings: (slug: string) => void
}

const ModuleGrid: React.FC<ModuleGridProps> = ({
  modules,
  onToggleModule,
  onOpenSettings,
}) => {
  return (
    <div className="dvc-modules">
      <div className="modules-grid">
        {Object.entries(modules).map(([slug, module]) => (
          <ModuleCard
            key={slug}
            module={module}
            onToggle={(enabled) => onToggleModule(slug, enabled)}
            onOpenSettings={() => onOpenSettings(slug)}
          />
        ))}
      </div>
    </div>
  )
}

export default ModuleGrid
