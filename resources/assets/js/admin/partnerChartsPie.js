import React, {Component} from "react";
import { ResponsiveContainer,Tooltip, PieChart, Pie, Sector, Cell, Legend} from 'recharts';


class PieCharts extends Component{

    constructor(props){
        super(props);
    }

    getRandomColor() {
        let max = 1 << 24;
        return '#' + (max + Math.floor(Math.random()*max)).toString(16).slice(-6);
    }

    render() {

        // let data = this.props.data;
        // this.props.data.map((item, key) =>
        //     let data = [{name: 'Active', value: parseInt(item.Active) }, {name: 'Cancelled', value: parseInt(item.Cancelled) }];      
        // );
        const data = [{name: 'Active', value: parseInt(this.props.data.Active)}
        , {name: 'Inactive', value: parseInt(this.props.data.Inactive)}
        , {name: 'Cancelled', value: parseInt(this.props.data.Cancelled)}
        , {name: 'Terminated', value: parseInt(this.props.data.Terminated)}

        ];
          

        const COLORS = ['#4073c4', '#ed8840',"#d61717","#300101"];
        const RADIAN = Math.PI / 180;    
        const renderCustomizedLabel = ({ cx, cy, midAngle, innerRadius, outerRadius, percent, index }) => {
        const radius = innerRadius + (outerRadius - innerRadius) * 0.5;
        const x  = cx + radius * Math.cos(-midAngle * RADIAN);
        const y = cy  + radius * Math.sin(-midAngle * RADIAN);
         
        return (
            <text x={x} y={y} fill="white" textAnchor={x > cx ? 'start' : 'end'}    dominantBaseline="central">
                {`${(percent * 100).toFixed(0)}%`}
            </text>
          );
        };

        const formatter = (value) => `${new Intl.NumberFormat('en').format(value)}`;
        return (
            <ResponsiveContainer height='100%' width='100%' aspect={3.0/2.0}>
                <PieChart width={800} height={400} onMouseEnter={this.onPieEnter}>
                        <Pie
                          data={data} 
                          dataKey="value"
                          cx={300} 
                          cy={200} 
                          labelLine={false}
                          label={renderCustomizedLabel}
                          outerRadius={170} 
                          fill="#8884d8"
                        >
                            {
                            data.map((entry, index) => <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]}/>)
                          }
                        </Pie>
                        <Legend />
                        <Tooltip />
                      </PieChart>
            </ResponsiveContainer>
        )
    }
}

export default PieCharts;

