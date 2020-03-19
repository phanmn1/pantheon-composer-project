import React, {Component} from 'react'
import ReactDOM from 'react-dom'
import shortid from 'shortid'
import 'whatwg-fetch'; 


class App extends Component {

    constructor() {
        super()
        this.state = {inputs: [], checked: false}
        this.initData(5)
        console.log(drupalSettings)

    }

    initData(num) {       
        for(var i = 0; i < num; ++i)
            this.state.inputs.push({
                id: shortid.generate(),
                JvPitTag: '', 
                message: '',
                success: false
        })        
    }

    reset(num){
        const array = []
        for(var i = 0; i < num; ++i)
            array.push({
                id: shortid.generate(),
                JvPitTag: '', 
                message: '',
                success: false
        })   
        this.setState({inputs: array})     
    }

    handleCheckboxChange = (event) => {
        this.setState({checked: event.target.checked})
    } 

    // 3DD.00774DEBA9

    async postData(item) {
        // const drupalURL = drupalSettings.path.currentPath
        // const uid = drupalSettings.drupalSettings.path.currentPath+

        //console.log("UID: "+ drupalSettings.user.uid)

        let data = await fetch('/dashboard/QA/JvPitTagUpdate/GetUserName/'+drupalSettings.user.uid, {
            method: 'GET', 
            headers: {'Content-Type': 'application/json'},
        })

        let username = await data.json()
        console.log(username.user)
        
        const response = await fetch(process.env.API_URL+"/api/TrapSamples/UpdateJvPitTag?JvPitTag=" + item.JvPitTag, {
                method: 'PATCH',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({replace: this.state.checked, user: username.user})
            })

            
        // let test = await response.json()
        // console.log(test)
        
        if(response.status == 400){
        //409 - CONFLICT
            const pitTagArray = [...this.state.inputs]
            pitTagArray.push({ id: shortid.generate(), JvPitTag: item.JvPitTag, message: 'Failure! Pit tag already exists', success: false})
            this.setState({inputs: pitTagArray})   
        } else if (response.status == 409){
        // 400 - NOT FOUND
            let promise = await response.json()
            console.log(promise)
            //const ladCodes = promise.join(',')
            let array = []
            promise.forEach((item) => {
               let date = new Date(Date.parse(item.PassDate))
               console.log(date.toDateString())
               array.push(item.LadCode+" - "+date.toDateString())
            })
            let string = array.join(', ')
            const pitTagArray = [...this.state.inputs]
            pitTagArray.push({ id: shortid.generate(), JvPitTag: item.JvPitTag, message: 'Failure! JvPitTag is duplicate! [ '+string+' ]', success: false})
            this.setState({inputs: pitTagArray})   
        } else if (response.status == 404) {
            const pitTagArray = [...this.state.inputs]
            pitTagArray.push({ id: shortid.generate(), JvPitTag: item.JvPitTag, message: 'Failure! JvPitTag Not found!', success: false})
            this.setState({inputs: pitTagArray})
        } else {
        // 200 - Success Successfully Patched
            const pitTagArray = [...this.state.inputs]
            pitTagArray.push({ id: shortid.generate(), JvPitTag: item.JvPitTag, message: 'Successfully migrated to PITTag', success: true})
            this.setState({inputs: pitTagArray})   
        }
    }


    changedValue(event, id) {
        const index = this.state.inputs.findIndex(i => i.id === id)
        const input = { ...this.state.inputs[index]}
        input.JvPitTag = event.target.value

        const items = [...this.state.inputs]
        items[index] = input
        this.setState({inputs: items})
    }
 
    sendAllUpdates() {
        // Javascript passes reference from arrays so must create 
        // a copy

        const JvPitTags = [...this.state.inputs]
        this.setState({inputs: []})
        //console.log(JvPitTags)
        JvPitTags.forEach( async (item) => {
            if(item.JvPitTag !== '' && !item.success)
              await this.postData(item)
        });

        // const sorted = [...this.state.inputs]
        // sorted.sort(this.compare)
        // console.log(sorted)
        // this.setState({inputs: sorted})

    }

    compare(a,b) {
        let comparison = 0
        if(a.success > b.success) 
            comparison = 1
       else if(a.success < b.success)
            comparison = -1

        return comparison
    }

    addRow() {
        const rows = [...this.state.inputs]
        rows.push({
            id: shortid.generate(),
            JvPitTag: '', 
            message: '',
            success: false
        })

        this.setState({inputs: rows})
    }

    render() {
        const renderBlock = {
            margin: '10px auto'
        }
            
        const render = (<div>
            {this.state.inputs.map((item) => {
                //console.log(item.success)

                const messageStyle = {
                    color: 'red',
                    margin: '4px'
                }
        
                const readOnly = {
                    backgroundColor: 'white'
                }
            
                messageStyle.color = item.success ? 'green' : 'red'
                readOnly.background = item.success ? 'gray' : 'white'
                return (<div className="input-label" key={item.id}>
                            <input type="text" 
                                defaultValue={item.JvPitTag} 
                                onChange={(event) => this.changedValue(event, item.id)} 
                                readOnly={item.success}
                                style={readOnly}>
                            </input>
                            <span style={messageStyle}>{item.message}</span>
                           
                            
                        </div>)


            })
            
            
            }
        </div>)
        
        return(
            
            <div style={renderBlock} >
                <div>
                    <button onClick={() => this.sendAllUpdates()}>Update</button>
                    <button onClick={() => this.reset(5)}>Clear</button>
                    <button onClick={() => this.addRow()}>Add Row</button>
                </div>
                <div><input type="checkbox" 
                            onChange={(event) => this.handleCheckboxChange(event)} 
                            name="jvpittag-overwrite"
                            checked={this.state.checked}>
                    </input>
                    <label htmlFor="jvpittag-overwrite">Overwrite PitTags if they already exist</label>
                </div>
                {render}
            </div>
        )

    }
}

ReactDOM.render(<App/>, document.getElementById('root'))