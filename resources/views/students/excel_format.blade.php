<table>
    <thead>
    <tr>
        <th>first_name</th>
        <th>last_name</th>
        <th>mobile</th>
        <th>gender</th>
        <th>dob</th>
        <th>admission_date</th>
        <th>guardian_email</th>
        <th>guardian_first_name</th>
        <th>guardian_last_name</th>
        <th>guardian_mobile</th>
        @if ($formFields)
            @foreach ($formFields as $item)
                @if ($item->type != 'file')
                    <th>{{$item->name}}</th>
                @endif
            @endforeach
        @endif
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Studnet 1</td>
        <td>TEST</td>
        <td>123456789</td>
        <td>
            <select>
                <option value="male">male</option>
                <option value="female">female</option>
            </select>
        </td>
        <td>10-10-2023</td>
        <td>10-10-2023</td>

        <td>guardiantest@example.com</td>
        <td>Guardian</td>
        <td>Test</td>
        <td>123456789</td>
        <td>10-10-2023</td>
        <td></td>

        @if(!empty($formFields))
            @foreach ($formFields as $key => $data)
                {{-- Text Field --}}
                @if($data->type == 'text')
                    <td>text</td>
                    {{-- Number Field --}}
                @elseif($data->type == 'number')
                    <td>number</td>

                    {{-- Dropdown Field --}}
                @elseif($data->type == 'dropdown')
                    <td>
                        <select>
                            @foreach (json_decode($data->default_values) as $data )
                                <option value="{{$data}}">{{$data}}</option>
                            @endforeach
                        </select>
                    </td>
                    {{-- Radio Field --}}
                @elseif($data->type == 'radio')
                    <td>
                        <select>
                            @foreach (json_decode($data->default_values) as $data )
                                <option value="{{$data}}">{{$data}}</option>
                            @endforeach
                        </select>
                    </td>

                    {{-- Checkbox Field --}}
                @elseif($data->type == 'checkbox')
                    <td>
                        <select>
                            @foreach (json_decode($data->default_values) as $data )
                                <option value="{{$data}}">{{$data}}</option>
                            @endforeach
                        </select>
                    </td>

                    {{-- Textarea Field --}}
                @elseif($data->type == 'textarea')
                    <td>TEXTAREA</td>
                @endif
            @endforeach
        @endif
    </tr>
    </tbody>
</table>
