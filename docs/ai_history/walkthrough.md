# Workshop Booking Implementation

## Summary
Implemented the Workshop Booking workflow with specific business rules: no payment, organization details required, and admin approval flow.

## Changes

### Database
- **New Migration**: `2025_12_12_150000_add_organization_fields_to_appointments_table.php` adds `organization_type` and `organization_other` columns to `appointments`.

### Frontend
- **[ServiceCard.vue](file:///c:/Users/r.pena007/.gemini/antigravity/scratch/therapy-app/resources/js/Components/ServiceCard.vue)**:
    - Hides price and "Add to Cart" button for `workshop`, `conference`, `talk`, and `training` types.
    - Adds "Reservar una reunión" button that redirects to the new workshop booking form.
- **[NEW] [WorkshopForm.vue](file:///c:/Users/r.pena007/.gemini/antigravity/scratch/therapy-app/resources/js/Pages/Booking/WorkshopForm.vue)**:
    - Custom booking form including "Company", "Government", "Educational", "NGO", and "Other" organization options.
    - Dynamic text field for "Other" organizations.

### Backend
- **[BookingController.php](file:///c:/Users/r.pena007/.gemini/antigravity/scratch/therapy-app/app/Http/Controllers/BookingController.php)**:
    - Added `showWorkshopForm` and `storeWorkshop` methods.
    - Updated `saveDate` to **skip payment** for workshop types.
    - Sets status to `pending` and triggers notification emails (currently commented out/placeholders) for admin approval.
- **Routes**: Registered `/booking/workshop` routes.

### Email & Scheduling Refinements
- **Emails**:
    -   **Client Confirmed**: Added Start/End times.
    -   **Client Pending**: Hidden bank details for workshops; specific WhatsApp contact message.
    -   **Admin Notification**: Hidden payment method for workshops; specific confirmation text.
- **Scheduling**:
    -   **Duration**: Workshop appointments now default to **45 minutes**.
    -   **Slot Generation**: `GoogleCalendarService` now generates slots dynamically based on the service duration (e.g., 45-min intervals for workshops).

## Verification
1.  **Run Migration**: `php artisan migrate` is required.
2.  **Test Flow**:
    -   Go to Services > Talleres.
    -   Click "Reservar una reunión".
    -   Fill form (select an organization).
    -   Select Date.
    -   Verify skip payment -> Success message.
