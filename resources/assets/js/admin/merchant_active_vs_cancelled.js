import React from "react";
import ReactDOM from "react-dom";
import DatePicker from "react-datepicker";
import axios from "axios";
 
import "react-datepicker/dist/react-datepicker.css";

import Charts from './partnerCharts';
import PieCharts from './partnerChartsPie';
import moment from 'moment';
import InvoiceCharts from './partnerInvoiceCharts';

class PartnerDash extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      merchantData: [],
    };

    this.updateDash = this.updateDash.bind(this);
    this.updateDash();
  }
 
  updateDash() {

    let url  = '/company/partner_dashboard_data/merchant_boarding_comparison';
    const config = {
        headers: { 
            'content-type': 'multipart/form-data',
            'X-Requested-With' : 'XMLHttpRequest'
        } 
    }
    let formData = new FormData();

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
 
  render() {
    return (
      <div>
        <div className="row">
            <div className="col-lg-12 col-md-3 col-sm-4">                 
              <Charts data = {this.state.merchantData} />
            </div>
        </div>
      </div>

    );
  }
}

ReactDOM.render(<PartnerDash />, document.getElementById('dashboard-body-merchants-active-vs-cancelled'));


