SELECT 
    *
FROM
    patient_portal_system.patient;
SELECT 
    *
FROM
    doctor;
SELECT 
    *
FROM
    booking;
use patient_portal_system;
SELECT 
    BIN_TO_UUID(idpatient)
FROM
    patient;
SELECT 
    BIN_TO_UUID(idpatient),
    first_name,
    middle_name,
    last_name,
    email_address,
    phone,
    birthdatetime,
    zipcode
FROM
    patient;
SELECT 
    BIN_TO_UUID(iddoctor),
    first_name,
    middle_name,
    last_name,
    email_address,
    phone,
    birthdatetime,
    zipcode
FROM
    doctor;
SELECT 
    BIN_TO_UUID(idbooking),
    BIN_TO_UUID(doctor),
    BIN_TO_UUID(patient),
    startdatetime,
    enddatetime,
    comments
FROM
    booking;