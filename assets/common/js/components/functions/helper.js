const axios = require("axios");

function processData(allText)
{
    let allTextLines = allText.split(/\r\n|\n/);
    let headers = allTextLines[0].split(';');
    let lines = [];

    for (let i=1; i<allTextLines.length; i++) {
        let data = allTextLines[i].split(';');

        lines.push({"cp": data[2], "city": data[1]});
    }

    return lines;
}

function getPostalCodes(self)
{
    axios.get( window.location.origin + "/postalcode.csv", {})
        .then(function (response) {
            self.setState({ arrayPostalCode: processData(response.data) })
        })
    ;
}

function setCityFromZipcode(self, e, arrayPostalCode)
{
    let name = e.currentTarget.name;
    let value = e.currentTarget.value;

    if(value.length <= 5){
        self.setState({ [name]: value })

        let v = ""
        if(arrayPostalCode.length !== 0){
            v = arrayPostalCode.filter(el => el.cp === value)

            if(v.length === 1){
                self.setState({ city: v[0].city })
            }
        }
    }
}

function setActive(tab, value)
{
    let active = "";
    tab.forEach(elem => {
        if(elem === value){
            active = " active";
        }
    })

    return active;
}

function setIncludeTimes(startHours, endHours, startMinutes, endMinutes)
{
    let includeTimes = [];
    for(let i=startHours; i <= endHours ; i++){
        for(let j=startMinutes; j <= endMinutes ; j++){
            let includeTime = new Date();
            includeTime.setHours(i); includeTime.setMinutes(j);
            includeTimes.push(includeTime)
        }
    }

    return includeTimes;
}

module.exports = {
    getPostalCodes,
    setCityFromZipcode,
    setActive,
    setIncludeTimes
}