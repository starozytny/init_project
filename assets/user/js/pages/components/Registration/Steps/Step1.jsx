import React from "react";

import { Button }   from "@dashboardComponents/Tools/Button";
import { TeamList } from "@userPages/components/Profil/Team/TeamList";

const CURRENT_STEP = 1;

export function Step1 ({ step, onNext, onSelectWorker, allWorkers, workers }) {
    return <div className={"step-section" + (step === CURRENT_STEP ? " active" : "")}>
        <TeamList isRegistration={true} onSelectWorker={onSelectWorker}
                  data={allWorkers} workers={workers} />

        <div className="line line-buttons">
            <div/>
            <div className="btns-submit">
                <Button onClick={() => onNext(2)}>Etape suivante</Button>
            </div>
        </div>
    </div>
}