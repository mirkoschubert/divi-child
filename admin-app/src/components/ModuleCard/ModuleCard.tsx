import { useState } from '@wordpress/element'
import {
  Card,
  CardHeader,
  CardBody,
  CardFooter,
  Button,
  ToggleControl
} from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import type { ModuleInfo } from '@/types'

import './ModuleCard.styl'

interface ModuleCardProps {
  module: ModuleInfo
  onToggle: (enabled: boolean) => Promise<void>
  onOpenSettings: () => void
}

const ModuleCard: React.FC<ModuleCardProps> = ({
  module,
  onToggle,
  onOpenSettings,
}) => {
  const [isLoading, setIsLoading] = useState(false)

  const handleToggle = async (enabled: boolean) => {
    setIsLoading(true)
    try {
      await onToggle(enabled)
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <Card
      className={`module ${module.enabled ? 'enabled' : 'disabled'} ${
        isLoading ? 'saving' : ''
      }`}
      size="small"
    >
      <CardHeader>
        <h3>{module.name}</h3>
        <ToggleControl
          checked={module.enabled}
          onChange={handleToggle}
          disabled={isLoading}
          __nextHasNoMarginBottom={true}
        />
      </CardHeader>
      <CardBody>
        <div className="module-content">
          <div className="module-info">
            <dl className="module-details">
              <dt>{__('Version', 'divi-child')}</dt>
              <dd>{module.version}</dd>

              <dt>{__('Author', 'divi-child')}</dt>
              <dd>{module.author}</dd>

              {/* <dt>{__('Requires Divi Version', 'divi-child')}</dt>
              <dd>{module.requires_divi_version}</dd> */}
            </dl>
            <p className="module-description">{module.description}</p>
          </div>
        </div>

      </CardBody>
      <CardFooter>
        {Object.keys(module.admin_settings).length > 1 && ( // Mehr als nur 'enabled'
          <Button
            className="settings-btn"
            onClick={onOpenSettings}
            disabled={!module.enabled || isLoading}
            variant="secondary"
          >
            {__('Settings', 'divi-child')}
          </Button>
        )}
      </CardFooter>
    </Card>
  )
}

export default ModuleCard
