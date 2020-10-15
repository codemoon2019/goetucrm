import React, {Component} from "react";
import {BarChart, LineChart, Bar, Line, XAxis, YAxis, Legend, CartesianGrid, Tooltip, ResponsiveContainer,Cell} from 'recharts';


class ProductCharts extends Component{

    constructor(props){
        super(props);
    }

    getRandomColor() {
        let max = 1 << 24;
        return '#' + (max + Math.floor(Math.random()*max)).toString(16).slice(-6);
    }

    render() {

        let data = this.props.data;

        // let barList = this.props.data.map((data) => 
        //     <Bar dataKey={data.value} stackId="a" fill={this.getRandomColor()} />
        // )

        const colors = ["#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd", "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf"];
        const formatter = (value) => `${new Intl.NumberFormat('en').format(value)}`;

        return (
            <ResponsiveContainer height='100%' width='100%' aspect={4.0/2.0}>
            <BarChart data={data} layout="vertical"
                margin={{top: 5, right: 30, left: 20, bottom: 5}}>
                <XAxis tickFormatter={formatter} type="number" tick={{fontSize: 10}}/>
                <YAxis dataKey="sub" type="category" tick={{fontSize: 10}} />
                <CartesianGrid strokeDasharray="3 3"/>
                <Tooltip formatter={formatter} />
                <Bar dataKey="qty" label={{ position: 'center' }}>
                 {
                    data.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={colors[index % 20]}/>
                    ))
                  }
                </Bar>
            </BarChart>
            </ResponsiveContainer>
        )
    }
}

export default ProductCharts;