import React, {Component} from "react";
import {BarChart, LineChart, Bar, Line, XAxis, YAxis, Legend, CartesianGrid, Tooltip, ResponsiveContainer} from 'recharts';


class Graph extends Component{

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
                margin={{top: 5, right: 30, left: 20, bottom: 5}} >
                <XAxis dataKey="name"/>
                <YAxis tickFormatter={formatter} />
                <CartesianGrid strokeDasharray="3 3"/>
                <Tooltip formatter={formatter} />
                <Bar name="Count as of Filter" dataKey="count"  fill="#ed8840" onClick={(data, index) => displayTable(data.name)} />
                <Bar name="Total" dataKey="total"  fill="#4073c4" onClick={(data, index) => displayTableTotal(data.name)} />
                <Legend />
            </BarChart>
            </ResponsiveContainer>
        )
    }
}

export default Graph;