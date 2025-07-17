function getEmployees(cboElement, employeeType, systemoneHRISIdAsEmpName){
    return new Promise((resolve, reject) => {
        let result = '<option value="0" disabled selected>Select One</option>';
        $.ajax({
            url: 'get_employees',
            method: 'get',
            data: {
                employeeType: employeeType,
            },
            dataType: 'json',
            beforeSend: function(){
                result = '<option value="0" disabled>Loading</option>';
                cboElement.html(result);
            },
            success: function(response){
                resolve(response)
                if(response['employeesData'].length > 0){
                    let position = "";
                    let division = "";
                    let department = "";
                    let section = "";
                    let fullName = "";
                    
                    result = '<option value="0" disabled selected>Select One</option>';
                    for(let index = 0; index < response['employeesData'].length; index++){
                        
                        if(response['employeesData'][index].FirstName != '' && response['employeesData'][index].LastName != ''){
                            fullName = `${response['employeesData'][index].FirstName} ${response['employeesData'][index].LastName}`;
                            // console.log('not null ', `${response['employeesData'][index].FirstName} ${response['employeesData'][index].LastName}`);
                        }else{
                            fullName = "";
                        }

                        if(response['employeesData'][index]['position_info'] != null){
                            position = response['employeesData'][index]['position_info'].Position;
                        }

                        if(response['employeesData'][index]['division_info'] != null){
                            division = response['employeesData'][index]['division_info'].Division;
                        }
                        
                        if(response['employeesData'][index]['department_info'] != null){
                            department = response['employeesData'][index]['department_info'].Department;
                        }

                        if(response['employeesData'][index]['section_info'] != null){
                            section = response['employeesData'][index]['section_info'].Section;
                        }

                        /**
                         * To exclude empty names on the database because of the testing data in the systemone hris
                         */
                        if(fullName != ""){
                            result += `<option value="${response['employeesData'][index].pkid}" employee-number="${response['employeesData'][index].EmpNo}" gender="${response['employeesData'][index].Gender}" position="${position}" division="${division}" department="${department}" section="${section}">${fullName}</option>`;
                        }
                    }
                }
                else{
                    result = '<option value="0" disabled>No record found</option>';
                }
                cboElement.html(result);
            },
            error: function(data, xhr, status){
                reject(data)
                result = '<option value="0" disabled>Reload Again</option>';
                cboElement.html(result);
                console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            }
        });
    });
}