export const formatDate = (dateString) => {
  const date = new Date(dateString);
  console.log(date);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(date);
}

export const isEditPage = (url)  => {
  return url?.includes('edit')
}

export const isReuploadPage = (url)  => {
  return url?.includes('reupload')
}

export const isRenewalPage = (url)  => {
  return url?.includes('renew')
}

export const formatDateShort = (dateString) => {
  const d = new Date(dateString)
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

export const checkDateStatus = (dateInput) => {
    const now = new Date();
    const givenDate = new Date(dateInput);
    const diffMs = now - givenDate; // difference in milliseconds

    const daysDiff = diffMs / (1000 * 60 * 60 * 24);

    return daysDiff > 1;
}

export const formatAmount = (amount) => {
  return `₱${Number(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

export const fullName = (data) => {
    if(data?.middle_name){
        return `${data?.first_name} ${data?.middle_name} ${data?.last_name}`;
    }
    return `${data?.first_name} ${data?.last_name}`;
};

export const mapTablesToGeoJSON = (tables) => {
  return {
    type: "FeatureCollection",
    features: tables.length === 0 ? [] : tables.map((table) => ({
      type: "Feature",
      geometry: {
        type: "Point",
        coordinates: table.coordinates || [0, 0], // default if null
      },
      properties: {
        Stall: table.name,
      },
    })),
  };
}

export const formatStallStatus = (id) => {
  let result;
  switch (id) {
    case 1:
      result = {
        color: 'green',
        status: 'Occupied'
      };
      break;
    case 3:
      result = {
        color: 'orange',
        status: 'Reserved'
      };
      break;  
    case 4:
      result = {
        color: 'red',
        status: 'Under Maintenance'
      };
      break; 
    default:
      result = {
        color: 'blue',
        status: 'Vacant'
      };
      break;
  }
  return result;
}

export const formatPaymentStatus = (data) => {
  if(data === 'Not Paid') {
    return 'red';
  }
  return 'blue';
}

export const formatFees = (fees, data, type) => {
  console.log('fees, data, type', fees, data, type);
  const fee = type ? fees : fees.filter((item) => item.id !== 3);
  const results = fee.map(f => f.id);

  if(type && data || type && data > 0){
    const bulbsExist = fee.find((item) => item.id === 3);
    if(!bulbsExist){
      results.push(fee.id);
    }
    return results;
  }

  if(data && !type){
    results.push(3);
    return results;
  }
  
  if(!data && type || data === 0 && type){
    return results.filter((item) => item !== 3);
  }
  return results;
}
