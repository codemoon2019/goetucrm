import React from "react";
import ReactDOM from "react-dom";
import DatePicker from "react-datepicker";
import axios from "axios";
 
import "react-datepicker/dist/react-datepicker.css";

import Charts from './appointmentsPerDayChart';
import moment from 'moment';

class PartnerDash extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      merchantData: [],
      startDate: moment().add(-90, 'days'),
      endDate: moment(),
    };

    this.updateDash = this.updateDash.bind(this);
    this.handleChangeStart= this.handleChangeStart.bind(this);
    this.handleChangeEnd= this.handleChangeEnd.bind(this);
    this.updateDash(moment().add(-30, 'days'),moment());
  }
 
  updateDash(start,end) {
    let formData = new FormData();
    formData.append('startDate',  start);
    formData.append('endDate',  end);
    let url  = '/company/partner_dashboard_data/appointments_per_day';
    const config = {
        headers: { 
            'content-type': 'multipart/form-data',
            'X-Requested-With' : 'XMLHttpRequest'
        } 
    }

    axios.post(url, formData, config)
    .then(response => {
            this.setState({
                merchantData: response.data.data
            })
    })
    .catch(error => {
        console.log(error.response)
    });


  }

  handleChangeStart(date)
  {
      this.setState({
          startDate: date
      });
      this.updateDash(date,this.state.endDate);
  }

  handleChangeEnd(date)
  {
      this.setState({
          endDate: date
      });
      this.updateDash(this.state.startDate,date);
  }


 
  render() {

    const selectionRange = {
        startDate: new Date(),
        endDate: new Date(),
        key: 'selection',
    }

    return (
      <div>

        <div className="row" style={{margin: 'auto'}}>
            <div className="col-md-3">
                <span>Start:</span>
                  <DatePicker
                      selected={this.state.startDate}
                      onChange={this.handleChangeStart}
                      className="form-control"
                  />
            </div>
            <div className="col-md-3">
                <span>End:</span>
                  <DatePicker
                      selected={this.state.endDate}
                      onChange={this.handleChangeEnd}
                      className="form-control"
                  />
            </div>

        </div>

        <div className="row">
            <div className="col-lg-12 col-md-3 col-sm-4">                 
              <Charts data = {this.state.merchantData} />
            </div>
        </div>
      </div>

    );
  }
}

ReactDOM.render(<PartnerDash />, document.getElementById('dashboard-body-appointments-per-day'));


