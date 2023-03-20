<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;


final class BaseData
{
    private function __construct(
        public string $AHV,
        public string $Anrede,
        public int $AnredeUniqueid,
        public string $CountryCode,
        public string $ElternAdresszusatz,
        public string $ElternAnrede,
        public int $ElternAnredeUniqueId,
        public int $ElternLandUniqueId,
        public string $ElternNachname,
        public string $ElternOrt,
        public string $ElternOrtUniqueId,
        public string $ElternPLZ,
        public string $ElternPostfach,
        public bool $ElternPostfachVorhanden,
        public string $ElternStrasse,
        public string $ElternVorname,
        public string $EmailPrivat,
        public string $Geburtstag,
        public string $GeneralNotes,
        public int $HeimatortUniqueId,
        public int $Identifikationsnummer,
        public int $KorrespondenzspraccheUniqueid,
        public string $Land,
        public int $LandUniqueId,
        public string $LastCompletedController,
        public string $Martikelnmmer,
        public int $MobilitaetHeimuniUniqueId,
        public int $MutterspracheUniqueId,
        public string $Nachname,
        public int $NationalitaetUniqueId,
        public bool $Parallelstudium,
        public int $PostEltern,
        public int $PostStudierend,
        public int $PruefingsmisserfolgMajor,
        public bool $QualificationStudiesAtUniversityOfBern,
        public int $RechnungEltern,
        public int $RechnungStudierend,
        public bool $RegistrationCompleted,
        public int $SemesterUniqueId,
        public string $StudentAdresszusatz,
        public string $StudentOrt,
        public int $StudentOrtUniqueId,
        public string $StudentPLZ,
        public string $StudentPostfach,
        public bool $StudentPostfachVorhanden,
        public string $StudentStrasse,
        public string $Studiengangsversion,
        public string $StudiengangsversionParallel,
        public int $StudiengangsversionParallelReqEcts,
        public int $StudiengangsversionParallelUniqueId,
        public int $StudiengangsversionReqEcts,
        public int $StudiengangsversionUniqueId,
        public string $Studienstruktur,
        public string $StudienstrukturParallel,
        public int $StudienstrukturParallelReqEcts,
        public int $StudienstrukturParallelUniqueId,
        public int $StudienstrukturReqEcts,
        public int $StudienstrukturUniqueId,
        public string $StudienstufeBFS,
        public int $StudienstufeUniqueId,
        public int $StudierendenkategorieUniqueId,
        public string $Telefon,
        public string $TelefonTyp,
        public int $UniqueId,
        public bool $VorbildungMasterabschluss,
        public string $Vorname,
        public string $Vorname2,
        public string $Vorname3,
        public string $WunschEinstufungsSemester,
    ) {
    }

    static function new(
        string $AHV,
        string $Anrede,
        int $AnredeUniqueid,
        string $CountryCode,
        string $ElternAdresszusatz,
        string $ElternAnrede,
        int $ElternAnredeUniqueId,
        int $ElternLandUniqueId,
        string $ElternNachname,
        string $ElternOrt,
        string $ElternOrtUniqueId,
        string $ElternPLZ,
        string $ElternPostfach,
        bool $ElternPostfachVorhanden,
        string $ElternStrasse,
        string $ElternVorname,
        string $EmailPrivat,
        string $Geburtstag,
        string $GeneralNotes,
        int $HeimatortUniqueId,
        int $Identifikationsnummer,
        int $KorrespondenzspraccheUniqueid,
        string $Land,
        int $LandUniqueId,
        string $LastCompletedController,
        string $Martikelnmmer,
        int $MobilitaetHeimuniUniqueId,
        int $MutterspracheUniqueId,
        string $Nachname,
        int $NationalitaetUniqueId,
        bool $Parallelstudium,
        int $PostEltern,
        int $PostStudierend,
        int $PruefingsmisserfolgMajor,
        bool $QualificationStudiesAtUniversityOfBern,
        int $RechnungEltern,
        int $RechnungStudierend,
        bool $RegistrationCompleted,
        int $SemesterUniqueId,
        string $StudentAdresszusatz,
        string $StudentOrt,
        int $StudentOrtUniqueId,
        string $StudentPLZ,
        string $StudentPostfach,
        bool $StudentPostfachVorhanden,
        string $StudentStrasse,
        string $Studiengangsversion,
        string $StudiengangsversionParallel,
        int $StudiengangsversionParallelReqEcts,
        int $StudiengangsversionParallelUniqueId,
        int $StudiengangsversionReqEcts,
        int $StudiengangsversionUniqueId,
        string $Studienstruktur,
        string $StudienstrukturParallel,
        int $StudienstrukturParallelReqEcts,
        int $StudienstrukturParallelUniqueId,
        int $StudienstrukturReqEcts,
        int $StudienstrukturUniqueId,
        string $StudienstufeBFS,
        int $StudienstufeUniqueId,
        int $StudierendenkategorieUniqueId,
        string $Telefon,
        string $TelefonTyp,
        int $UniqueId,
        bool $VorbildungMasterabschluss,
        string $Vorname,
        string $Vorname2,
        string $Vorname3,
        string $WunschEinstufungsSemester,
    ) : self {

        return new self(
            ...get_defined_vars()
        );
    }
}