import CaseApiController from './CaseApiController'
import EmployerApiController from './EmployerApiController'
import ContactPersonApiController from './ContactPersonApiController'
import ContractApiController from './ContractApiController'
import OrganizationalUnitApiController from './OrganizationalUnitApiController'
import EmployeeApiController from './EmployeeApiController'
import EmployeeImportApiController from './EmployeeImportApiController'

const Api = {
    CaseApiController: Object.assign(CaseApiController, CaseApiController),
    EmployerApiController: Object.assign(EmployerApiController, EmployerApiController),
    ContactPersonApiController: Object.assign(ContactPersonApiController, ContactPersonApiController),
    ContractApiController: Object.assign(ContractApiController, ContractApiController),
    OrganizationalUnitApiController: Object.assign(OrganizationalUnitApiController, OrganizationalUnitApiController),
    EmployeeApiController: Object.assign(EmployeeApiController, EmployeeApiController),
    EmployeeImportApiController: Object.assign(EmployeeImportApiController, EmployeeImportApiController),
}

export default Api