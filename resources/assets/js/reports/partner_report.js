import React from "react";
import ReactDOM from "react-dom";
import DatePicker from "react-datepicker";
import axios from "axios";
 
import "react-datepicker/dist/react-datepicker.css";

import Charts from './partner_report_charts';
import Graph from './partner_report_graph';


class PartnerCountGraph extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      merchantData: [],
    };

    this.updateGraph = this.updateGraph.bind(this);
    this.updateGraph();
  }
 
  updateGraph() {
    var $from;
    var $to;
    var $url  = '/billing/report_new_partner_graph_data/'+ $('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}'
   if($('#txtDateType').val() == 'Daily'){
      $from = $('#txtDate').val().trim();
      $to = 'none';
   }

   if($('#txtDateType').val() == 'Weekly'){
      $from = $('#weeklyFrom').val().trim();
      $to = $('#weeklyTo').val().trim();             
   }

   if($('#txtDateType').val() == 'Monthly'){
      $from = $('#txtMonthlyDate').val().trim();
      $to = 'none';                
   }

   if($('#txtDateType').val() == 'Yearly'){
      $from = $('#txtYearlyDate').val().trim();
      $to = 'none';                
   }

   if($('#txtDateType').val() == 'Custom'){
      $from = $('#txtFromDate').val().trim();
      $to = $('#txtToDate').val().trim();                
   }
   $url = $url.replace("{$from}",$from);
   $url = $url.replace("{$to}",$to);

    const config = {
        headers: { 
            'content-type': 'multipart/form-data',
            'X-Requested-With' : 'XMLHttpRequest'
        } 
    }
    let formData = new FormData();

    axios.post($url, formData, config)
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
              <Graph data = {this.state.merchantData} />
            </div>
        </div>
      </div>

    );
  }
}

ReactDOM.render(<PartnerCountGraph />, document.getElementById('partner-count-graph'));

class ProductSalesGraph extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      merchantData: [],
    };

    this.handleChangeId = this.handleChangeId.bind(this);
    this.updateDash = this.updateDash.bind(this);
  }

  handleChangeId() {
    this.updateDash();
  }

 
  updateDash() {
    var $from;
    var $to;
    var $url  = '/billing/report_new_partner_product_data/'+$('#prodId').val()+'/'+ $('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}'
   if($('#txtDateType').val() == 'Daily'){
      $from = $('#txtDate').val().trim();
      $to = 'none';
   }

   if($('#txtDateType').val() == 'Weekly'){
      $from = $('#weeklyFrom').val().trim();
      $to = $('#weeklyTo').val().trim();             
   }

   if($('#txtDateType').val() == 'Monthly'){
      $from = $('#txtMonthlyDate').val().trim();
      $to = 'none';                
   }

   if($('#txtDateType').val() == 'Yearly'){
      $from = $('#txtYearlyDate').val().trim();
      $to = 'none';                
   }

   if($('#txtDateType').val() == 'Custom'){
      $from = $('#txtFromDate').val().trim();
      $to = $('#txtToDate').val().trim();                
   }
   $url = $url.replace("{$from}",$from);
   $url = $url.replace("{$to}",$to);

    const config = {
        headers: { 
            'content-type': 'multipart/form-data',
            'X-Requested-With' : 'XMLHttpRequest'
        } 
    }
    let formData = new FormData();

    axios.post($url, formData, config)
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
            <button id="change-id" style={{display: 'none'}}
              onClick={this.handleChangeId}
            />
            <div className="col-lg-12 col-md-3 col-sm-4">                 
              <Charts data = {this.state.merchantData} />
            </div>
        </div>
      </div>

    );
  }
}

ReactDOM.render(<ProductSalesGraph />, document.getElementById('productSalesGraph'));


