import React from "react";

import { FormActions } from "@userPages/components/Registration/Registration";
import { BanksList }   from "@userPages/components/Profil/Bank/BanksList";

const CURRENT_STEP = 2;

export function Step2 ({ step, errors, onNext, onSelectBank, onOpenAside, allBanks, bank }) {
    return <div className={"step-section step-workers" + (step === CURRENT_STEP ? " active" : "")}>

        <BanksList isRegistration={true} data={allBanks} bank={bank} onSelectBank={onSelectBank} onOpenAside={onOpenAside} />

        <FormActions onNext={onNext} currentStep={CURRENT_STEP} />
    </div>
}