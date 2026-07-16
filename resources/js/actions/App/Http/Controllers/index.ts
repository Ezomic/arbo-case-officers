import Api from './Api';
import AbsenceDashboardController from './AbsenceDashboardController';
import EmployerController from './EmployerController';
import ContactPersonController from './ContactPersonController';
import EmployeeController from './EmployeeController';
import GdprExportController from './GdprExportController';
import CaseController from './CaseController';
import CaseAssignmentController from './CaseAssignmentController';
import CaseNoteController from './CaseNoteController';
import CaseTaskController from './CaseTaskController';
import ContractController from './ContractController';
import OrganizationalUnitController from './OrganizationalUnitController';
import UserController from './UserController';
import Settings from './Settings';

const Controllers = {
    Api: Object.assign(Api, Api),
    AbsenceDashboardController: Object.assign(
        AbsenceDashboardController,
        AbsenceDashboardController,
    ),
    EmployerController: Object.assign(EmployerController, EmployerController),
    ContactPersonController: Object.assign(
        ContactPersonController,
        ContactPersonController,
    ),
    EmployeeController: Object.assign(EmployeeController, EmployeeController),
    GdprExportController: Object.assign(
        GdprExportController,
        GdprExportController,
    ),
    CaseController: Object.assign(CaseController, CaseController),
    CaseAssignmentController: Object.assign(
        CaseAssignmentController,
        CaseAssignmentController,
    ),
    CaseNoteController: Object.assign(CaseNoteController, CaseNoteController),
    CaseTaskController: Object.assign(CaseTaskController, CaseTaskController),
    ContractController: Object.assign(ContractController, ContractController),
    OrganizationalUnitController: Object.assign(
        OrganizationalUnitController,
        OrganizationalUnitController,
    ),
    UserController: Object.assign(UserController, UserController),
    Settings: Object.assign(Settings, Settings),
};

export default Controllers;
