# Workshop Booking Implementation Plan

## Goal
Implement a specific booking workflow for Workshops that excludes payment, requires "Organization" details, and enforces Admin approval.

## User Review Required
> [!IMPORTANT]
> The "Organization" field includes an "Other" option which triggers a dynamic text field.
> Payment step is skipped entirely for Workshops; status goes directly to "Pending Admin Approval".
> Prices are hidden for Workshops.

## Proposed Changes

### Database
#### [NEW] Migration
- Create a new migration to add `organization_type` (string, nullable) and `organization_other` (string, nullable) to the `appointments` table.

### Frontend
#### [MODIFY] [ServiceCard.vue](file:///c:/Users/r.pena007/.gemini/antigravity/scratch/therapy-app/resources/js/Components/ServiceCard.vue)
-   Hide price and "Add to Cart" button if service type is `workshop`, `conference`, `talk`, or `training`.
-   Change main action button text to "Reservar una reunión" (Book a meeting) for these types.
-   Redirect to `/booking/workshop` instead of `/booking/individual` or `/booking/group`.

#### [NEW] [WorkshopForm.vue](file:///c:/Users/r.pena007/.gemini/antigravity/scratch/therapy-app/resources/js/Pages/Booking/WorkshopForm.vue)
-   Clone `GroupForm.vue` or `IndividualForm.vue` as a base.
-   Add "Organization" dropdown:
    -   Empresa
    -   Institución de gobierno
    -   Institución no gubernamental
    -   Institución educativa
    -   Otros
-   Add dynamic text input if "Otros" is selected.
-   Collect standard contact info (Name, Email, Phone).

### Backend
#### [MODIFY] [BookingController.php](file:///c:/Users/r.pena007/.gemini/antigravity/scratch/therapy-app/app/Http/Controllers/BookingController.php)
-   Add `showWorkshopForm` method.
-   Add `storeWorkshop` method:
    -   Validate organization fields.
    -   Create appointment with status `pending`.
    -   Redirect to `booking.date` (Select Date).

#### [MODIFY] [BookingController.php](file:///c:/Users/r.pena007/.gemini/antigravity/scratch/therapy-app/app/Http/Controllers/BookingController.php) (Date Selection Logic)
-   Update `saveDate`:
    -   If service type is workshop-related, SKIP payment.
    -   Set status to `pending_approval` (or keep `pending` but trigger specific emails).
    -   Redirect to a simplified "Confirmation / Pending Approval" page instead of Payment.

### Routes
-   Add `GET /booking/workshop` and `POST /booking/workshop`.

## Verification Plan
### Manual Verification
1.  **UI Check**: Go to `/services`, check "Talleres" tab. Verify no price/cart button, and "Reservar una reunión" button exists.
2.  **Form Check**: Click "Reservar...". Verify Workshop Form appears with Organization fields. Test "Otros" dynamic field.
3.  **Flow Check**: Submit form -> Select Date. Verify skipping payment -> Success page.
4.  **Database Check**: Verify `appointments` table has correct `organization_type`.
