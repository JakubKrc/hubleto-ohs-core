import App from '@hubleto/react-ui/core/App'

import TableCars from './Components/FC/TableCars'

class OhsCoreApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('OhsCoreTableCars', TableCars);

    // For Hubleto inputs the Ohs apps do not use (e.g. the customer's "Manažér").
    globalThis.hubleto.registerReactComponent('OhsCoreNoInput', () => null);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/External/WaiBlue/OhsCore', new OhsCoreApp());
