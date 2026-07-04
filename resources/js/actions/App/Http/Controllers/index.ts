import Api from './Api'
import EmployerController from './EmployerController'
import EmployeeController from './EmployeeController'
import CaseController from './CaseController'
import CaseNoteController from './CaseNoteController'
import ContractController from './ContractController'
import OrganizationalUnitController from './OrganizationalUnitController'
import UserController from './UserController'
import Settings from './Settings'

const Controllers = {
    Api: Object.assign(Api, Api),
    EmployerController: Object.assign(EmployerController, EmployerController),
    EmployeeController: Object.assign(EmployeeController, EmployeeController),
    CaseController: Object.assign(CaseController, CaseController),
    CaseNoteController: Object.assign(CaseNoteController, CaseNoteController),
    ContractController: Object.assign(ContractController, ContractController),
    OrganizationalUnitController: Object.assign(OrganizationalUnitController, OrganizationalUnitController),
    UserController: Object.assign(UserController, UserController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers