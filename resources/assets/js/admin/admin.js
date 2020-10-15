import React from "react";
import ReactDOM from "react-dom";
import axios from "axios";

// Import React Table
import ReactTable from "react-table";
import "react-table/react-table.css";
import checkboxHOC from "react-table/lib/hoc/selectTable";

import DatePicker from "react-datepicker";
import 'react-datepicker/dist/react-datepicker.css';

//Graph
import Charts from './charts';

import moment from 'moment';

const CheckboxTable = checkboxHOC(ReactTable);

class ViewmoreSection extends React.Component {
    constructor(props) {
        super(props);
        var d = new Date();
        var n = d.getMonth()+1;
        var y = d.getFullYear();

        this.state = {
           boxClass: "box box-default collapsed-box",
           viewLess: "hide-label",
           viewMore: "show-label",
           years: [],
           months: [],
           startYear: y,
           startMonth: n,
           partnerTypes: [],
           partners: [],
           productList: [],
           partnerList: [],
           partnerData: [],
           products: [],
           data: [],
           chartData: {},
            startDate: moment().add(-30, 'days'),
            endDate: moment(),
            selectedMerchant: [],
            selected: {},
          barData: [    ]
        }
        this.viewMore = this.viewMore.bind(this);
        this.toggleRow = this.toggleRow.bind(this);
        this.handleChangeStart= this.handleChangeStart.bind(this);
        this.handleChangeEnd= this.handleChangeEnd.bind(this);
    }


    componentDidMount() {
        axios.get('/company/partnertype')
            .then(response => {
                this.setState({ 
                    partnerTypes: response.data,
                })
            })
        // console.log(this.refs.partnerId.value);
    }

    componentDidUpdate(){

    }


    viewMore(e)
    {
        e.preventDefault();

        this.setState({
            boxClass : this.state.boxClass === 'box box-default' ?
            'box box-default collapsed-box' : 'box box-default'
        });

        this.setState({
            viewMore : this.state.boxClass === 'box box-default' ?
            'show-label' : 'hide-label'
        });

        this.setState({
            viewLess : this.state.boxClass === 'box box-default' ?
            'hide-label' : 'show-label'
        });


        var d = new Date();
        var n = d.getMonth()+1;
        var y = d.getFullYear();

        let formData = new FormData();
        formData.append('companyId', this.props.companyId);
        formData.append('year',  y);
        formData.append('month',  n);
        let url  = '/company/dashbord_data/top_partners';
        const config = {
            headers: { 
                'content-type': 'multipart/form-data',
                'X-Requested-With' : 'XMLHttpRequest'
            } 
        }
        axios.post(url, formData, config)
        .then(response => {
                this.setState({
                    partners: response.data.data
                })
                merchantColumns = getMerchantColumns(response.data.data);
        })
        .catch(error => {
            console.log(error.response)
            /** @todo Handle errors */

        })

        url  = '/company/dashbord_data/top_products';
        axios.post(url, formData, config)
        .then(response => {
                this.setState({
                    products: response.data.data
                })
        })
        .catch(error => {
            console.log(error.response)
            /** @todo Handle errors */

        });

        url  = '/company/dashbord_data/top_products_bar';
        axios.post(url, formData, config)
        .then(response => {              
                this.setState({
                    productList: response.data.data
                });
                this.setState({
                    barData: response.data
                });

        })
        .catch(error => {
            console.log(error.response)
            /** @todo Handle errors */
        })

        url  = '/company/dashbord_data/top_partners_bar';
        axios.post(url, formData, config)
        .then(response => {              
                this.setState({
                    partnerList: response.data.data
                });
                this.setState({
                    partnerData: response.data
                });

        })
        .catch(error => {
            console.log(error.response)
            /** @todo Handle errors */
        })

        var years = [];
        var months = [];

        months.push({value: 1 , label: 'January'});
        months.push({value: 2 , label: 'February'});
        months.push({value: 3 , label: 'March'});
        months.push({value: 4 , label: 'April'});
        months.push({value: 5 , label: 'May'});
        months.push({value: 6 , label: 'June'});
        months.push({value: 7 , label: 'July'});
        months.push({value: 8 , label: 'August'});
        months.push({value: 9 , label: 'September'});
        months.push({value: 10 , label: 'October'});
        months.push({value: 11 , label: 'November'});
        months.push({value: 12 , label: 'December'});

        for (let i = y-10; i <= y; i++) {
            years.push({
                value: i
            });
        }
        this.setState({
            years: years
        });
        this.setState({
            months: months
        });
        this.setState({startYear: y});
        this.setState({startMonth: n});

       
    }

    showPartners(e)
    {
        let formData = new FormData();
        formData.append('companyId', this.props.companyId);
        formData.append('year',  this.refs.year.value);
        formData.append('month',  this.refs.month.value);

        let url  = '/company/dashbord_data/top_partners';
        const config = {
            headers: { 
                'content-type': 'multipart/form-data',
                'X-Requested-With' : 'XMLHttpRequest'
            } 
        }
        axios.post(url, formData, config)
        .then(response => {
                this.setState({
                    partners: response.data.data
                })
        })
        .catch(error => {
            console.log(error.response)
            /** @todo Handle errors */

        });

        url  = '/company/dashbord_data/top_products';
        axios.post(url, formData, config)
        .then(response => {
                this.setState({
                    products: response.data.data
                })
        })
        .catch(error => {
            console.log(error.response)
            /** @todo Handle errors */

        });

        url  = '/company/dashbord_data/top_products_bar';
        axios.post(url, formData, config)
        .then(response => {              
                this.setState({
                    productList: response.data.data
                });
                this.setState({
                    barData: response.data
                });

        })
        .catch(error => {
            console.log(error.response)
            /** @todo Handle errors */

        })

        url  = '/company/dashbord_data/top_partners_bar';
        axios.post(url, formData, config)
        .then(response => {              
                this.setState({
                    partnerList: response.data.data
                });
                this.setState({
                    partnerData: response.data
                });

        })
        .catch(error => {
            console.log(error.response)
            /** @todo Handle errors */

        })
        this.setState({startYear: this.refs.year.value});
        this.setState({startMonth: this.refs.month.value});
    }

    handleChangeStart(date)
    {
        this.setState({
            startDate: date
        })
    }

    handleChangeEnd(date)
    {
        this.setState({
            endDate: date
        })
    }

    /**
    * Toggle a single checkbox for select table
    */
    toggleSelection(key: number, shift: string, row: string) {
        // start off with the existing state
        let selectedMerchant = [...this.state.selectedMerchant];
        const keyIndex = selection.indexOf(key);

        // check to see if the key exists
        if (keyIndex >= 0) {
            // it does exist so we will remove it using destructing
            selection = [
                ...selectedMerchant.slice(0, keyIndex),
                ...selectedMerchant.slice(keyIndex + 1)
            ];
        } else {
            // it does not exist so add it
            selectedMerchant.push(key);
        }
        // update the state
        this.setState({ selectedMerchant });
    }
     
    toggleRow(id) {
        const newSelected = Object.assign({}, this.state.selected);
        newSelected[id] = !this.state.selected[id];
        this.setState({
            selected: newSelected,
            
        });
        
        let formData = new FormData();
        let partnerId = id;
        formData.append('partnerId', partnerId);
        formData.append('companyId', this.props.companyId);
        let url  = '/company/partner/receipts';
        const config = {
            headers: { 
                'content-type': 'multipart/form-data',
                'X-Requested-With' : 'XMLHttpRequest'
            } 
        }
        axios.post(url, formData, config)
        .then(response => {
                
        })
        .catch(error => {
            console.log(error.response)
            /** @todo Handle errors */

        })
    }

    graph(){

    }


    render() {
        const selectionRange = {
            startDate: new Date(),
            endDate: new Date(),
            key: 'selection',
        }
        return(
            <div className={this.state.boxClass}
                 style={{margin: 0 +" !important"}} >
                <div className="box-header with-border">
                    <h3 className="box-title"
                        style={{textAlign: "center", display: "block"}}>
                        <a className="view-more" href="#" onClick={this.viewMore}><label className={this.state.viewMore}>View More</label><label className={this.state.viewLess}>View Less</label></a>
                    </h3>
                </div>

                <div className="box-body" id="view-more">
                    <div className="row" style={{margin: 'auto'}}>
                        <div className="col-md-3">
                            <span>Month:</span>
                            <select className="form-control" ref="month" value={this.state.startMonth} onChange={this.showPartners.bind(this)} >
                                { this.state.months.map(month => <option key={month.value} value={month.value} >{month.label}</option>)}
                            </select>
                        </div>
                        <div className="col-md-3">
                            <span>Year:</span>
                            <select className="form-control" ref="year" value={this.state.startYear} onChange={this.showPartners.bind(this)} >
                                 { this.state.years.map(year => <option key={year.value} value={year.value} >{year.value}</option>)}

                            </select>
                        </div>

                    </div>
                    <div className="row" style={{margin: 'auto'}}>
                        <div className="col-md-6" style={{margin: 'auto'}}>
                            <div className="box box-primary">
                                <div className="box-header">
                                    <h3 className="box-title title-header">
                                        Top Partners
                                    </h3>
                                </div>
                                {/* Partners */}
                                <div className="box-body merchant-table">
                                <div>
                                        <ReactTable
                                        toggleSelection={this.toggleSelection}
                                        data={this.state.partners}
                                        columns={[
                                            {
                                            Header: "Id",
                                            columns: [
                                                {
                                                accessor: "id",
                                                width: 50
                                                },
                                            ]
                                            },
                                            {
                                            Header: "Agent",
                                            columns: [
                                                {
                                                accessor: "fullName",
                                                width: 200
                                                },
                                            ]
                                            },
                                            {
                                            Header: "Sales",
                                            columns: [
                                                {
                                                accessor: "totalSale",
                                                width: 100,
                                                className:"numeric-data",
                                                Cell: row => (
                                                     
                                                       "USD " + row.value
                                                     
                                                  )
                                                },
                                            ]
                                            },
                                        ]}
                                        defaultPageSize={5}
                                        className="-striped -highlight"
                                        />
                                    </div>
                                    </div>
                                    </div>
                                    </div>
                                    <div className="col-md-6" style={{margin: 'auto'}}>
                                    <div className="box box-primary">
                                        <div className="box-header">
                                            <h3 className="box-title title-header">
                                                Top Products
                                            </h3>
                                        </div>
                                    {/** Products Table */}
                                    <div className="box-body product-table">
                                        <ReactTable
                                        data={this.state.products}
                                        columns={[
                                            {
                                            Header: "Name",
                                            columns: [
                                                {
                                                accessor: "name"
                                                },
                                            ]
                                            },
                                            {
                                            Header: "Sales",
                                            columns: [
                                                {
                                                accessor: "totalSale",
                                                width: 150,
                                                className:"numeric-data",
                                                Cell: row => (
                                                     
                                                       "USD " + row.value
                                                     
                                                  )
                                                },
                                            ]
                                            },
                                        ]}
                                        defaultPageSize={5}
                                        className="-striped -highlight"
                                        />
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-md-6" style={{margin: 'auto'}}>
                                    <div className="box box-info">
                                        <div className="box-header with-border">
                                            <h3 className="box-title title-header">Partner Sale</h3>
                                            <div className="box-body">
                                            
                                            <Charts data = {this.state.partnerData}
                                                    products = {this.state.partnerList} />
                                            
                                            </div>  
                                        </div>
                                    </div>
                                </div>

                                <div className="col-md-6" style={{margin: 'auto'}}>
                                    <div className="box box-info">
                                        <div className="box-header with-border">
                                            <h3 className="box-title title-header">Product Sale</h3>
                                            <div className="box-body">
                                            
                                            <Charts data = {this.state.barData}
                                                    products = {this.state.productList} />
                                            
                                            </div>  
                                        </div>
                                    </div>
                                </div>

                            </div>
                            </div>
                            </div>
        )
    }
}

let viewmoreSections = document.getElementsByClassName('view_more')
for (var i = 0; i < viewmoreSections.length; i++) {
    ReactDOM.render(<ViewmoreSection companyId={viewmoreSections[i].dataset.companyid} />, viewmoreSections[i])
}
