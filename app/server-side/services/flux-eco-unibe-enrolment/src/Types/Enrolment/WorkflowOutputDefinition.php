<?php

namespace FluxEco\UnibeEnrolment\Types\Enrolment;

// back-mapping "parents-address": true,
// "parents-address-same-address": false,

use FluxEcoType\FluxEcoAttributeDefinition;

final readonly class WorkflowOutputDefinition
{

    private function __construct(
        public FluxEcoAttributeDefinition $oldAgeSurvivarInsuranceNumber,
        public FluxEcoAttributeDefinition $salutation,
        public FluxEcoAttributeDefinition $parentsAddressExtraAddressLine,
        public FluxEcoAttributeDefinition $parentsAddressSalutation,
        public FluxEcoAttributeDefinition $parentsAddressCountry,
        public FluxEcoAttributeDefinition $parentsAddressLastName,
        public FluxEcoAttributeDefinition $parentsAddressPlace,
        public FluxEcoAttributeDefinition $parentsAddressPostalCode,
        public FluxEcoAttributeDefinition $parentsAddressStreet,
        public FluxEcoAttributeDefinition $parentsAddressHouseNumber,
        public FluxEcoAttributeDefinition $parentsAddressFirstNames,
        public FluxEcoAttributeDefinition $email,
        public FluxEcoAttributeDefinition $birthDate,
        public FluxEcoAttributeDefinition $generalnotes, //
        public FluxEcoAttributeDefinition $originPlace,
        public FluxEcoAttributeDefinition $correspondenceLanguage,
        public FluxEcoAttributeDefinition $country,
        public FluxEcoAttributeDefinition $registrationNumber,
        public FluxEcoAttributeDefinition $mobilitaetheimuniuniqueid, //
        public FluxEcoAttributeDefinition $motherLanguage,
        public FluxEcoAttributeDefinition $lastName,
        public FluxEcoAttributeDefinition $nationally,
        public FluxEcoAttributeDefinition $parallelstudium, //
        public FluxEcoAttributeDefinition $parentsAddressGeneralPost,
        public FluxEcoAttributeDefinition $pruefingsmisserfolgmajor, //
        public FluxEcoAttributeDefinition $qualificationstudiesatuniversityofbern, //
        public FluxEcoAttributeDefinition $parentsAddressInvoices,
        public FluxEcoAttributeDefinition $semester,
        public FluxEcoAttributeDefinition $extraAddressLine,
        public FluxEcoAttributeDefinition $place,
        public FluxEcoAttributeDefinition $postalCode,
        public FluxEcoAttributeDefinition $postalOfficeBox,
        public FluxEcoAttributeDefinition $street,
        public FluxEcoAttributeDefinition $studiengangsversionparalleluniqueid, //
        public FluxEcoAttributeDefinition $subject,
        public FluxEcoAttributeDefinition $studienstrukturparalleluniqueid, //
        public FluxEcoAttributeDefinition $combination,
        public FluxEcoAttributeDefinition $studienstufebfs, //
        public FluxEcoAttributeDefinition $degreeProgram,
        public FluxEcoAttributeDefinition $studierendenkategorieuniqueid, //
        public FluxEcoAttributeDefinition $homePhoneAreaCode,
        public FluxEcoAttributeDefinition $homePhoneNumber,
        public FluxEcoAttributeDefinition $mobilePhoneAreaCode,
        public FluxEcoAttributeDefinition $mobilePhoneNumber,
        public FluxEcoAttributeDefinition $businessPhoneAreaCode,
        public FluxEcoAttributeDefinition $businessPhoneNumber,
        public FluxEcoAttributeDefinition $vorbildungmasterabschluss, //
        public FluxEcoAttributeDefinition $firstName,
        public FluxEcoAttributeDefinition $secondFirstName,
        public FluxEcoAttributeDefinition $additionalFirstNames,
        public FluxEcoAttributeDefinition $wunscheinstufungssemester, //
        public FluxEcoAttributeDefinition $houseNumber
    )
    {

    }

    public static function new(): self
    {
        return new self(
            FluxEcoAttributeDefinition::new("old-age-survivar-insurance-number", "string"),
            FluxEcoAttributeDefinition::new("salutation", "int"),
            FluxEcoAttributeDefinition::new("parents-address-extra-address-line", "string"),
            FluxEcoAttributeDefinition::new("parents-address-salutation,", "int"),
            FluxEcoAttributeDefinition::new("parents-address-country", "int"),
            FluxEcoAttributeDefinition::new("parents-address-last-name", "string"),
            FluxEcoAttributeDefinition::new("parents-address-place", "int"),
            FluxEcoAttributeDefinition::new("parents-address-postal-code", "string"),
            FluxEcoAttributeDefinition::new("parents-address-street", "string"),
            FluxEcoAttributeDefinition::new("parents-address-house-number", "int"),
            FluxEcoAttributeDefinition::new("parents-address-first-names", "string"),
            FluxEcoAttributeDefinition::new("email", "string"),
            FluxEcoAttributeDefinition::new("birth-date", "string"),
            FluxEcoAttributeDefinition::new("GeneralNotes", "string"),
            FluxEcoAttributeDefinition::new("origin-place", "int"),
            FluxEcoAttributeDefinition::new("correspondence-language", "int"),
            FluxEcoAttributeDefinition::new("country", "int"),
            FluxEcoAttributeDefinition::new("LastCompletedController", "string"),
            FluxEcoAttributeDefinition::new("MobilitaetHeimuniUniqueId", "int"),
            FluxEcoAttributeDefinition::new("mother-language", "int"),
            FluxEcoAttributeDefinition::new("last-name", "string"),
            FluxEcoAttributeDefinition::new("nationally", "int"),
            FluxEcoAttributeDefinition::new("Parallelstudium", "string"),
            FluxEcoAttributeDefinition::new("parents-address-general-post", "bool"),
            FluxEcoAttributeDefinition::new("PruefingsmisserfolgMajor", "string"),
            FluxEcoAttributeDefinition::new("QualificationStudiesAtUniversityOfBern", "string"),
            FluxEcoAttributeDefinition::new("parents-address-invoices", "bool"),
            FluxEcoAttributeDefinition::new("semester", "int"),
            FluxEcoAttributeDefinition::new("extra-address-line", "string"),
            FluxEcoAttributeDefinition::new("place", "int"),
            FluxEcoAttributeDefinition::new("postal-code", "string"),
            FluxEcoAttributeDefinition::new("postal-office-box", "string"),
            FluxEcoAttributeDefinition::new("street", "string"),
            FluxEcoAttributeDefinition::new("StudiengangsversionParallelUniqueId", "int"),
            FluxEcoAttributeDefinition::new("subject", "int"),
            FluxEcoAttributeDefinition::new("StudienstrukturParallelUniqueId", "int"),
            FluxEcoAttributeDefinition::new("combination", "int"),
            FluxEcoAttributeDefinition::new("StudienstufeBFS", "string"),
            FluxEcoAttributeDefinition::new("degree-program", "int"),
            FluxEcoAttributeDefinition::new("StudierendenkategorieUniqueId", "int"),
            FluxEcoAttributeDefinition::new("home-phone-area-code", "string"),
            FluxEcoAttributeDefinition::new("home-phone-number", "string"),
            FluxEcoAttributeDefinition::new("mobile-phone-area-code", "string"),
            FluxEcoAttributeDefinition::new("mobile-phone-number", "string"),
            FluxEcoAttributeDefinition::new("business-phone-area-code", "string"),
            FluxEcoAttributeDefinition::new("business-phone-number", "string"),
            FluxEcoAttributeDefinition::new("VorbildungMasterabschluss", "string"),
            FluxEcoAttributeDefinition::new("first-name", "string"),
            FluxEcoAttributeDefinition::new("second-first-name", "string"),
            FluxEcoAttributeDefinition::new("additional-first-names", "array"),
            FluxEcoAttributeDefinition::new("WunschEinstufungsSemester", "string"),
            FluxEcoAttributeDefinition::new("house-number", "int"),
        );
    }
}