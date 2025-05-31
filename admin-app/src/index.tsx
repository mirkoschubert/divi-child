import { render } from '@wordpress/element'
import App from './App'


// Warten bis DOM bereit ist
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('divi-child-react-admin')
  if (container) {
    render(<App />, container)
  }
})
