import React from "react";
import ReactDOM from "react-dom";
import DatePicker from "react-datepicker";
import axios from "axios";
 
import "react-datepicker/dist/react-datepicker.css";

import Charts from './partnerCharts';
import PieCharts from './partnerChartsPie';
import moment from 'moment';
import InvoiceCharts from './partnerInvoiceCharts';

class PartnerDashInvoice extends React.Component {
  constructor(props) {
    super(props);

    var statusFilter = [];
    statusFilter.push({value: 'A' , label: 'All'});
    statusFilter.push({value: 'P' , label: 'Paid'});
    statusFilter.push({value: 'U' , label: 'Unpaid'});
    statusFilter.push({value: 'L' , label: 'Partial Paid'});

    this.state = {
      startDate:  moment().add(-30, 'days'),
      endDate: moment(),
      salesData: [],
      statusFilter: statusFilter,
      currentFilter:'All'
    };

    this.updateDash = this.updateDash.bind(this);
    this.handleChangeFrom = this.handleChangeFrom.bind(this);
    this.handleChangeTo= this.handleChangeTo.bind(this);
    this.handleChangeFilter= this.handleChangeFilter.bind(this);
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

  handleChangeFilter(e) {
    this.setState({
      currentFilter: this.refs.statusfilter.value
    });
    this.updateDash(this.state.startDate.format('YYYY-MM-DD'),this.state.endDate.format('YYYY-MM-DD'));
  }

  updateDash(from,to,filter = "") {
    let formData = new FormData();
    formData.append('from',  from);
    formData.append('to',  to);
    if(filter == ""){
      formData.append('filter',  this.refs.statusfilter.value);
    }else{
      formData.append('filter', filter);
    }

    let url  = '/company/partner_dashboard_data/periodic_sales';
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
              <select className="form-control" ref="statusfilter" value={this.state.currentFilter} onChange={this.handleChangeFilter} >
                  { this.state.statusFilter.map(statusFilter => <option key={statusFilter.value} value={statusFilter.value} >{statusFilter.label}</option>)}
              </select>
          </div>
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
              <InvoiceCharts data = {this.state.salesData} />
            </div>
        </div>
      </div>

    );
  }
}

ReactDOM.render(<PartnerDashInvoice />, document.getElementById('dashboard-body-invoice'));