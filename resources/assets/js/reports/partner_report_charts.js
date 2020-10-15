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

        let barList = this.props.data.map((product) => 
            <Bar key={product.id} dataKey={product.name} stackId="a" fill={this.getRandomColor()} />
        )

        return (
            <ResponsiveContainer height='100%' width='100%' aspect={4.0/2.0}>
            <BarChart data={data}
                margin={{top: 5, right: 30, left: 20, bottom: 5}}>
                <XAxis dataKey="name"/>
                <YAxis />
                
                <CartesianGrid strokeDasharray="3 3"/>
                <Tooltip/>
                
                <Bar dataKey="value" fill="#4073c4"/>
            </BarChart>
            </ResponsiveContainer>
        )
    }
}

export default Charts;