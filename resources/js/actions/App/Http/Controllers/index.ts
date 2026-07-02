import WeekMenuController from './WeekMenuController'
import Settings from './Settings'
import Admin from './Admin'

const Controllers = {
    WeekMenuController: Object.assign(WeekMenuController, WeekMenuController),
    Settings: Object.assign(Settings, Settings),
    Admin: Object.assign(Admin, Admin),
}

export default Controllers