import React, {Component} from "react";
import {BarChart, LineChart, Bar, Line, XAxis, YAxis, Legend, CartesianGrid, Tooltip, ResponsiveContainer} from 'recharts';


class Charts extends Component{

    constructor(props){
        super(props);
    }

    getRandomColor() {
        let max = 1 << 24;
        return '#' + (max + Math.floor(Math.random()*max)).toString(16).slice(-6);
    }

    render() {

        let data = this.props.data;
        const formatter = (value) => `${new Intl.NumberFormat('en').format(value)}`;
        return (
            <ResponsiveContainer height='100%' width='100%' aspect={3.0/2.0}>
            <BarChart data={data}
                margin={{top: 5, right: 30, left: 20, bottom: 5}} layout= 'vertical'>
                <XAxis type="number" tickFormatter={formatter} />
                <YAxis  type="category"  dataKey="assignee"/>
                <CartesianGrid strokeDasharray="3 3"/>
                <Tooltip formatter={formatter} />
                <Bar name="Converted" dataKey="prospect" stackId="a" fill="#ed8840" layout= 'vertical'/>
                <Bar name="Leads" dataKey="leads" stackId="a" fill="#4073c4" layout= 'vertical'/>
                <Legend />
            </BarChart>
            </ResponsiveContainer>
        )
    }
}

export default Charts;