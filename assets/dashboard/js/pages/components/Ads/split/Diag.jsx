import React     from "react";
import Sanitize  from "@dashboardComponents/functions/sanitaze";

export function Diag({ elem }){
    let content = <div>Le diagnostic de performance énergétique et d'indice d'émission de gaz à effet de serre n'ont pas été soumis pour le moment.</div>
    if(elem.diagnostic){
        content = <>
            <div className="details-tab-infos-main">
                <DiagSimple isDpe={true} elem={elem}/>
                <DiagDetails isDpe={true} elem={elem}/>
            </div>

            <div className="details-tab-infos-main">
                <DiagSimple isDpe={false} elem={elem}/>
                <DiagDetails isDpe={false} elem={elem}/>
            </div>
        </>
    }

    return (<div className="details-tab-infos">
        {content}
    </div>)
}

function DiagSimple({ isDpe, elem })
{
    let letters = ["A", "B", "C", "D", "E", "F", "G"];
    let title = isDpe ? "Diagnostic de performance énergétique" : "Indice d'émission de gaz à effet de serre";
    let classDiag = isDpe ? "dpe" : "ges";

    return (
        <div className="diagnostic">
            <div>
                <div className="diag-title">{title}</div>
                <div className={"diag-" + classDiag}>
                    {letters.map(le => {
                        return <div key={le}>
                            <div className={classDiag + " " + classDiag + "-" + le.toLowerCase()}>
                                <div>{le}</div>
                            </div>
                            <div className="number">5</div>
                        </div>
                    })}
                </div>
            </div>
        </div>
    )
}

function DiagDetails({ isDpe, elem })
{
    let lettersDetails = [
        { le :"A", valDpe: "≤ 50", valGes: "≤ 5" },
        { le :"B", valDpe: "51 à 90", valGes: "6 à 10" },
        { le :"C", valDpe: "91 à 150", valGes: "11 à 20" },
        { le :"D", valDpe: "151 à 230", valGes: "21 à 35" },
        { le :"E", valDpe: "231 à 330", valGes: "36 à 55" },
        { le :"F", valDpe: "331 à 450", valGes: "56 à 80" },
        { le :"G", valDpe: "> 450", valGes: "> 80" }
    ]
    let title = isDpe ? "Diagnostic de performance énergétique" : "Indice d'émission de gaz à effet de serre";
    let classDiag = isDpe ? "dpe" : "ges";

    return (
        <div className="diagnostic-details">
            <div>
                <div className="diag-title">{title} en kWhEP/m2.an</div>
                <div className={"diag-" + classDiag}>
                    {lettersDetails.map(le => {
                        return <div key={le.le}>
                            <div className={classDiag + " " + classDiag + "-" + le.le.toLowerCase()}>
                                <div>{isDpe ? le.valDpe : le.valGes}</div>
                                <div>{le.le}</div>
                            </div>
                            <div className="number">5</div>
                        </div>
                    })}
                </div>
            </div>
        </div>
    )
}