import React from "react";
import ReactDOM from "react-dom";
import DatePicker from "react-datepicker";
import axios from "axios";
 
import "react-datepicker/dist/react-datepicker.css";

import moment from 'moment';
import SalesProfitCharts from './salesProfitCharts';

class PartnerDashSalesProfit extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      startDate:  moment().add(-30, 'days'),
      endDate: moment(),
      salesData: [],
      currentFilter:'All'
    };

    this.updateDash = this.updateDash.bind(this);
    this.handleChangeFrom = this.handleChangeFrom.bind(this);
    this.handleChangeTo= this.handleChangeTo.bind(this);
    this.updateDash(this.state.startDate.format('YYYY-MM-DD'),this.state.endDate.format('YYYY-MM-DD'),'All');
  }
 

  handleChangeFrom(date) {
    this.setState({
      startDate: date
    });
    this.updateDash(date.format('YYYY-MM-DD'),this.state.endDate.format('YYYY-MM-DD'));
  }

  handleChangeTo(date) {
    this.setState({
      endDate: date
    });
    this.updateDash(this.state.startDate.format('YYYY-MM-DD'),date.format('YYYY-MM-DD'));
  }


  updateDash(from,to,filter = "") {
    let formData = new FormData();
    formData.append('from',  from);
    formData.append('to',  to);

    let url  = '/company/partner_dashboard_data/sales_profit';
    const config = {
        headers: { 
            'content-type': 'multipart/form-data',
            'X-Requested-With' : 'XMLHttpRequest'
        } 
    }
    axios.post(url, formData, config)
    .then(response => {
            this.setState({
                salesData: response.data.data
            })
    })
    .catch(error => {
        console.log(error.response)
    });


  }
 
  render() {
    return (
      <div>
        <div className="row" style={{paddingLeft: '10px'}}>
          <div className="col-lg-4 col-md-3 col-sm-4">
            <DatePicker
              selected={this.state.startDate}
              onChange={this.handleChangeFrom}
              className="form-control"
            />
          </div>
          <div className="col-lg-4 col-md-3 col-sm-4">
            <DatePicker
              selected={this.state.endDate}
              onChange={this.handleChangeTo}
              className="form-control"
            />
          </div>
        </div>

        <div className="row">
            <div className="col-lg-12 col-md-3 col-sm-4">                 
              <SalesProfitCharts data = {this.state.salesData} />
            </div>
        </div>
      </div>

    );
  }
}

ReactDOM.render(<PartnerDashSalesProfit />, document.getElementById('dashboard-body-sales-profit'));