function setToString(hours, minutes){
    return (hours !== 0 ? (hours + "h ") : "") + (minutes !== 0 ? (minutes + "min") : "");
}

function getIntervalTime(end, start)
{
    let nInterval = ""
    if((end !== null && end !== "") && (start !== null && start !== "")){
        let timeStartHours = parseInt(start.getHours());
        let timeStartMinutes = parseInt(start.getMinutes());

        let timeEndHours = parseInt(end.getHours());
        let timeEndMinutes = parseInt(end.getMinutes());

        if(timeEndHours > timeStartHours && timeEndMinutes === 0){
            timeEndHours = timeEndHours - 1;
            timeEndMinutes = 60;
        }

        let intervalHours = timeEndHours - timeStartHours;
        let intervalMinutes = timeEndMinutes - timeStartMinutes;

        if(intervalMinutes === 60){
            intervalHours = intervalHours + 1;
            intervalMinutes = 0;
        }

        nInterval = setToString(intervalHours, intervalMinutes);
    }

    return nInterval;
}

function getHoursMinutes(duration)
{
    let timeDurationHours = 0;
    let timeDurationMinutes = 0;

    if(duration){
        let nDuration = duration.replaceAll("h", "") ;
        nDuration = nDuration.replaceAll("min", "") ;

        let timeDuration = nDuration.split(" ");

        if(timeDuration.length >= 2){
            timeDurationHours = timeDuration[0];
            timeDurationMinutes = timeDuration[1];
        }else{
            if(duration.indexOf("h") !== -1){
                timeDurationHours = timeDuration[0];
            }else if(duration.indexOf("min") !== -1){
                timeDurationMinutes = timeDuration[0];
            }
        }
    }

    return [parseInt(timeDurationHours !== "" ? timeDurationHours : 0), parseInt(timeDurationMinutes !== "" ? timeDurationMinutes : 0)];
}

function getDurationTotal(duration, duration2)
{
    let nDuration = getHoursMinutes(duration);
    let nDuration2 = getHoursMinutes(duration2);

    return setToString(nDuration[0] + nDuration2[0], nDuration[1] + nDuration2[1]);
}

function extractDateToArray(date){
    if(date !== "" && date !== null){
        let string = date.toLocaleString('fr-FR').slice(0,10).replace(/-/g,'');
        string = string.split('/');


        if(string.length !== 3){
            return "";
        }

        return [parseInt(string[0]), parseInt(string[1]), parseInt(string[2])];
    }

    return "";
}

function getDurationByDay(duration, duration2, start, end)
{
    if(start !== null && start !== "" && end !== null && end !== ""){
        let nDuration = getHoursMinutes(duration);
        let nDuration2 = getHoursMinutes(duration2);

        let startArray = extractDateToArray(start);
        let endArray = extractDateToArray(end);

        return setToString(nDuration[0] + nDuration2[0], nDuration[1] + nDuration2[1]);
    }

    return "";
}

module.exports = {
    getIntervalTime,
    getDurationTotal,
    getDurationByDay
}